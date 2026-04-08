<style>
  /* Scoped admin report pages: prevent content from breaking the admin shell */
  .content {
    width: 100%;
    max-width: 100%;
    min-width: 0;
  }

  .report-container {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: hidden;
  }

  /* Prevent common media overflow */
  .report-container img,
  .report-container svg,
  .report-container canvas,
  .report-container video,
  .report-container iframe {
    max-width: 100%;
  }

  /* Make long strings wrap instead of expanding the page */
  .report-container * {
    overflow-wrap: anywhere;
  }

  /* Ensure flex/grid children can shrink */
  .report-container .row,
  .report-container .d-flex,
  .report-container .rc-page-header,
  .report-container .rc-gen-grid {
    min-width: 0;
  }

  /* Wide elements must scroll locally, never force page width */
  .report-container .table-responsive,
  .report-container .rc-table-wrap {
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .report-container table {
    max-width: 100%;
  }
</style>

