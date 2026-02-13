<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<title>Admin System</title>
<link rel="icon" type="image/png" sizes="32x32" href="https://devs.onllyons.com/crm/assets/new_image/logo.png">
<meta name="theme-color" content="#ffffff">
<script src="/crm/assets/js/config.js"></script>
<script src="/crm/vendors/simplebar/simplebar.min.js"></script>


<link href="/crm/vendors/choices/choices.min.css" rel="stylesheet">
<link href="/crm/vendors/prism/prism-okaidia.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="/crm/vendors/datatables-full-plugin/toastr.min.css" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
<link href="/crm/vendors/simplebar/simplebar.min.css" rel="stylesheet">
<link href="/crm/assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl">
<link href="/crm/assets/css/theme.min.css" rel="stylesheet" id="style-default">
<link href="/crm/assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl">
<link href="/crm/assets/css/user.min.css" rel="stylesheet" id="user-style-default">
<link rel="stylesheet" type="text/css" href="/crm/assets/css/demo-sk.css">
<link href="/crm/vendors/flatpickr/flatpickr.min.css" rel="stylesheet" />
<!-- url -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
<!-- <link rel="stylesheet" type="text/css" href="https://prium.github.io/falcon/v3.24.0/assets/css/theme.min.css"> -->

<link href="/crm/vendors/toastr/toastr.min.css" rel="stylesheet">
<script>
  var isRTL = JSON.parse(localStorage.getItem('isRTL'));
  if (isRTL) {
    var linkDefault = document.getElementById('style-default');
    var userLinkDefault = document.getElementById('user-style-default');
    linkDefault.setAttribute('disabled', true);
    userLinkDefault.setAttribute('disabled', true);
    document.querySelector('html').setAttribute('dir', 'rtl');
  } else { 
    var linkRTL = document.getElementById('style-rtl');
    var userLinkRTL = document.getElementById('user-style-rtl');
    linkRTL.setAttribute('disabled', true);
    userLinkRTL.setAttribute('disabled', true);
  }
</script>