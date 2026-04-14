<style>
  /* Scoped admin report pages: prevent content from breaking the admin shell */
  .content {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
  }

  .report-container {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: hidden;
    box-sizing: border-box;
  }

  /* Ensure inner wrapper has proper constraints */
  .report-container > div {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
    overflow: visible;
  }

  /* Prevent common media overflow */
  .report-container img,
  .report-container svg,
  .report-container canvas,
  .report-container video,
  .report-container iframe {
    max-width: 100%;
    height: auto;
    display: block;
  }

  /* Make long strings wrap instead of expanding the page */
  .report-container * {
    overflow-wrap: break-word;
    word-wrap: break-word;
  }

  /* Ensure flex/grid children can shrink */
  .report-container .row,
  .report-container .d-flex,
  .report-container .rc-page-header,
  .report-container .rc-gen-grid,
  .report-container .rc-card,
  .report-container .rc-card__body {
    min-width: 0;
    box-sizing: border-box;
  }

  /* Wide elements must scroll locally, never force page width */
  .report-container .table-responsive,
  .report-container .rc-table-wrap {
    max-width: 100%;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-sizing: border-box;
  }

  .report-container table {
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
  }

  /* Fix form and button containers */
  .report-container form,
  .report-container .rc-gen-grid {
    width: 100%;
    box-sizing: border-box;
  }

  /* Ensure all child elements respect parent bounds */
  .report-container .rc-card {
    width: 100%;
    box-sizing: border-box;
    max-width: 100%;
  }

  .report-container .rc-card__body {
    width: 100%;
    box-sizing: border-box;
    max-width: 100%;
  }
</style>

