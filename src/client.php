<DOCTYPE html>
  <html lang='en'>
  <body>
    <div align='center'></div>
    <form method='POST'>
      <table>
        <tr>
          <td>
            <label>Ticket ID</label>
            <input type="text" name="ticketID"></br>
            <label>Estimation</label>
            <input type="text" name="estimation"></br>
            <input type="submit" name="confirm" value="confirm">
          </td>
        </tr>

        <?php
          $host = "0.0.0.0";
          $port = 8004;

          if(isset($_POST['confirm'])){
            $estimation = $_POST['estimation'];
            $ticketID = $_POST['ticketID'];
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, $host, $port);
            socket_write($sock, $estimation, $ticketID);


          }
        ?>

      </table>
    </form>
  </div>

  </body>
</html>
