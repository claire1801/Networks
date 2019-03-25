<?php

	$host = "0.0.0.0";
	$port = 8004;
	set_time_limit(0);


	$sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	$result = socket_bind($sock, $host, $port) or die("Could not bind to socket\n");

		$result = socket_listen($sock, 3) or die("Could not set up socket listener\n");
    echo "Listening for connections";

    class Chat
    {
      function readline()
        {
          return rtrim(fgets(STDIN));
        }
    }
    do
    {
      $accept = socket_accept($sock) or die("Couldn't accept incoming connection");
      $estimation = socket_read($accept, 1024) or die("Could not read input estimation\n");
//			$ticketID = socket_read($accept, 1024) or die("Could not read input ticketID\n");
      $output = 'Estimation received';
      $estimation = trim($estimation);
//			$ticketID = trim($ticketID);

      echo " Developer estimation:\t".$estimation."\n Ticket ID:\t".$ticketID;


      socket_write($accept, $output, strlen($output)) or die("Could not write output");
    } while (true);

    socket_close($accept, $sock);



?>
