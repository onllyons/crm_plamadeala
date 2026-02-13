<?php
   require_once $_SERVER['DOCUMENT_ROOT'] . "/crm/backend/all_include.php"; checkAuth();
   ?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">
   <head>
      <?php include '../assets/components/links.php' ?>
      <link rel="stylesheet" type="text/css" href="/crm/vendors/calendar/main.min.css">
      <link rel="stylesheet" type="text/css" href="/crm/pages/packs/calendar/css.css">
   </head>
   <body>
      <main class="main" id="top">
         <div class="container" data-layout="container">
            <?php include '../assets/components/nav-left.php' ?>
            <div class="content">
               <?php include '../assets/components/nav-top.php' ?>
               <div class="card overflow-hidden">
                       
                        <div class="card-body p-0 scrollbar">
                            <div class="calendar-outline" id="appCalendar"></div>
                        </div>
                    </div>
               <?php include '../assets/components/footer.php' ?>
            </div>
         </div>
      </main>
      
     
      <?php include '../assets/components/off-canvas-design.php' ?>
      <?php include '../assets/components/scripts.php' ?>

<script src="/crm/vendors/calendar/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('appCalendar');
    if (!el) return;

    const calendar = new FullCalendar.Calendar(el, {
      locale: 'ro',
      initialView: 'dayGridMonth',
      height: 'auto',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
      },
      events: '/crm/pages/packs/calendar/get_projects_calendar.php',
      eventColor: '#04A6A5',
      eventTextColor: '#fff',
      eventClick: function(info) {
        info.jsEvent.preventDefault();
        if (info.event.url) window.open(info.event.url, '_blank');
      }
    });

    calendar.render();
  });
</script>







   </body>
</html>