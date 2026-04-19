<!DOCTYPE html>
<html>
   <body>
      <script>
         function myPopup(myURL, title, myWidth, myHeight) {
            var left = (screen.width - myWidth) / 2;
            var top = (screen.height - myHeight) / 4;
            var myWindow = window.open(myURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + myWidth + ', height=' + myHeight + ', top=' + top + ', left=' + left);
         }
      </script>
      <button onclick=" myPopup ('http://www.qries.com', 'web', 1050, 550);">Open Qries.com</button>
   </body>
</html>