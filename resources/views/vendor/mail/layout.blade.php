<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css" rel="stylesheet" media="all">
@media only screen and (max-width: 620px) {
  table[class=body] h1 {
    font-size: 28px !important;
    margin-bottom: 10px !important;
  }
  table[class=body] p,
  table[class=body] ul,
  table[class=body] ol,
  table[class=body] td,
  table[class=body] span,
  table[class=body] a {
    font-size: 16px !important;
    line-height: 1.4 !important;
  }
  table[class=body] {
    width: 100% !important;
    min-width: 100% !important;
  }
  table[class=body] .wrapper {
    width: 100%;
    min-width: 100%;
  }
  table[class=body] .footer {
    width: 100%;
    min-width: 100%;
  }
  table[class=body] .footer td,
  table[class=body] .footer p {
    padding: 20px !important;
    font-size: 12px !important;
    line-height: 1.5 !important;
  }
}
@media all {
  .ExternalClass {
    width: 100%;
  }
  .ExternalClass,
  .ExternalClass p,
  .ExternalClass span,
  .ExternalClass font,
  .ExternalClass td,
  .ExternalClass div {
    line-height: 100%;
  }
  .apple-link a {
    color: inherit !important;
    text-decoration: none !important;
  }
  #MessageViewBody a {
    color: inherit;
    text-decoration: none;
    font-size: inherit;
    font-weight: inherit;
    line-height: inherit;
  }
  .btn-primary table td:hover {
    background-color: #34495e !important;
  }
  .btn-primary a:hover {
    background-color: #34495e !important;
    border-color: #34495e !important;
  }
}
</style>
</head>
<body class="" style="background-color: #f9fafb; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f9fafb; width: 100%;">
  <tr>
    <td style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: top; padding: 0;">&nbsp;</td>
    <td class="container" style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;">
      <!-- start main content block -->
      <table role="presentation" class="main" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: white; border-radius: 3px; width: 100%;">
        <tr>
          <td class="wrapper" style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: top; box-sizing: border-box; padding: 20px;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
              <tr>
                <td style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: top;">
                  {{ $slot }}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

      <!-- start footer -->
      <div class="footer" style="clear: both; margin-top: 10px; text-align: center; width: 100%;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
          <tr>
            <td class="content-cell" style="font-family: Helvetica, Arial, sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; text-align: center; font-size: 12px; color: #6b7280;">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </td>
          </tr>
        </table>
      </div>
    </td>
    <td style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: top; padding: 0;">&nbsp;</td>
  </tr>
</table>
</body>
</html>
