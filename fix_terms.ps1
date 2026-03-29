# PowerShell script to fix "No Term Assigned" issue

try {
    # Connect to database
    $connectionString = "server=127.0.0.1;port=3307;database=esa;uid=root;pwd="
    $connection = New-Object MySql.Data.MySqlClient.MySqlConnection($connectionString)
    $connection.Open()
    
    Write-Host "✅ Connected to database" -ForegroundColor Green
    
    # Get first term as default
    $command = $connection.CreateCommand()
    $command.CommandText = "SELECT id, name FROM terms ORDER BY id LIMIT 1"
    $reader = $command.ExecuteReader()
    
    if ($reader.Read()) {
        $defaultTermId = $reader.GetInt32(0)
        $defaultTermName = $reader.GetString(1)
        $reader.Close()
        
        Write-Host "✅ Using default term: $defaultTermName (ID: $defaultTermId)" -ForegroundColor Green
        
        # Update marks with missing terms
        $command.CommandText = "UPDATE marks SET term_id = @termId WHERE term_id IS NULL OR term_id = 0"
        $command.Parameters.AddWithValue("@termId", $defaultTermId) | Out-Null
        $updatedCount = $command.ExecuteNonQuery()
        
        Write-Host "✅ Updated $updatedCount marks with default term" -ForegroundColor Green
        
        # Verify fix
        $command.CommandText = "SELECT COUNT(*) as count FROM marks WHERE term_id IS NULL OR term_id = 0"
        $remainingNull = $command.ExecuteScalar()
        
        if ($remainingNull -eq 0) {
            Write-Host "🎉 SUCCESS: All marks now have valid term assignments!" -ForegroundColor Green
            Write-Host "The teacher dashboard should no longer show 'No Term Assigned'" -ForegroundColor Green
        } else {
            Write-Host "⚠️  Still $remainingNull marks need manual review" -ForegroundColor Yellow
        }
    } else {
        $reader.Close()
        Write-Host "❌ No terms found in database" -ForegroundColor Red
        Write-Host "Please create terms first in the admin panel" -ForegroundColor Yellow
    }
    
    $connection.Close()
    
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Possible solutions:" -ForegroundColor Yellow
    Write-Host "1. Make sure MySQL is running on 127.0.0.1:3307" -ForegroundColor White
    Write-Host "2. Check your .env file database settings" -ForegroundColor White
    Write-Host "3. Run: php artisan migrate" -ForegroundColor White
    Write-Host "4. Install MySQL Connector for PowerShell if needed" -ForegroundColor White
}

Write-Host ""
Write-Host "Script completed. You can now refresh your teacher dashboard." -ForegroundColor Cyan
