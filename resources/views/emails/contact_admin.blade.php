<!-- filepath: resources/views/emails/contact_admin.blade.php -->
<p><strong>Name:</strong> {{ $username }}</p>
<p><strong>Phone:</strong> {{ $phone }}</p>
<p><strong>Email:</strong> {{ $email }}</p>
<p><strong>Subject:</strong> {{ $subject }}</p>
<p><strong>Message:</strong></p>
<p>{{ $content }}</p>