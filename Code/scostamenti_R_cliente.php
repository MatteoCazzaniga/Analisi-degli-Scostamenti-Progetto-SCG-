<?php
include_once("intestazione.php");
include_once("connessione.php");
    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    
	$sql1="SELECT vb.NrOrigine, vb.TotaleVenditab, (vb.TotaleVenditab/vb.quantitab)* vc.quantitac as Mixeff, vc.TotaleVenditac
FROM (select BudgetConsuntivo, NrOrigine, sum(TotaleVendita) as TotaleVenditab, sum(quantita) as quantitab from Vendite_Corrette where BudgetConsuntivo='BUDGET' group by NrOrigine) as vb join (select NrOrigine, BudgetConsuntivo, sum(TotaleVendita) as TotaleVenditac, sum(quantita) as quantitac from Vendite_Corrette where BudgetConsuntivo='CONSUNTIVO' group by NrOrigine) as vc on vb.NrOrigine=vc.NrOrigine
order by vb.NrOrigine;";
    $result1= $conn->query($sql1);
    
    $sql2="select NrOrigine, TotaleVendita as TotaleVenditac
from Vendite_Corrette
where BudgetConsuntivo='CONSUNTIVO' and NrOrigine not in (select NrOrigine from Vendite_Corrette where BudgetConsuntivo='BUDGET' group by NrOrigine)
group by NrOrigine
order by NrOrigine;";
	$result2=$conn->query($sql2);
    
    $sql3="select NrOrigine, TotaleVendita as TotaleVenditab
from Vendite_Corrette
where BudgetConsuntivo='BUDGET' and NrOrigine not in (select NrOrigine from Vendite_Corrette where BudgetConsuntivo='CONSUNTIVO' group by NrOrigine)
group by NrOrigine
order by NrOrigine;";
	$result3=$conn->query($sql3);

    
    if ($result1->num_rows > 0) {
    	echo '<head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Analisi degli Scostamenti</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body id="page-top">
    
    
    
    
        <!-- Presentazione-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="sideNav">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">
                <span class="d-block d-lg-none">Progetto Di Sistemi di Controllo e Di Gestione</span>
                <span class="d-none d-lg-block"><img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="assets/img/profile.jpg" alt="..." /></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="scostamenti_R_Articolo.php">Scostamento R per Articolo</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#">Scostamento R per Cliente</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="scostamenti_R_Paese.php">Scostamento R per Valuta</a></li>
                </ul>
            </div>
        </nav>';
        echo"<table class='table'>
                  <thead class='thead-dark'>
                      <tr>
                        <th scope='col'> Codice Cliente</th>
                      	<th scope='col'> Budget</th>
                      	<th scope='col'> ΔV</th>
                      	<th scope='col'> Mix Eff</th>
                      	<th scope='col'> ΔPz</th>
                      	<th scope='col'> Consuntivo</th>
                      </tr>
                  </thead>
                  <tbody id='clienti'>";
         while($dati = $result1->fetch_assoc()) {
                      echo '<tr>
                      	<td class="cCentrato"> '. $dati['NrOrigine'].'</td>
                      	<td class="cCentrato"> '. round($dati['TotaleVenditab'],2).'</td>
                        <td class="cCentrato"> '. round($dati['Mixeff']-$dati['TotaleVenditab'],2).'</td>
						<td class="cCentrato"> '. round($dati['Mixeff'],2).'</td>
                        <td class="cCentrato"> '. round($dati['TotaleVenditac']-$dati['Mixeff'],2).'</td>
                        <td class="cCentrato"> '. round($dati['TotaleVenditac'],2).'</td>
                      </tr>';
                  }
		while($dati3 = $result3->fetch_assoc()) {
                      echo '<tr>
                      	<td class="cCentrato"> '. $dati3['NrOrigine'].'</td>
                      	<td class="cCentrato"> '. round($dati3['TotaleVenditab'],2).'</td>
                        <td class="cCentrato"> '. round(-$dati3['TotaleVenditab'],2).'</td>
						<td class="cCentrato"> 0</td>
                        <td class="cCentrato"> 0</td>
                        <td class="cCentrato"> 0 </td>
                      </tr>';
                  }
         while($dati2 = $result2->fetch_assoc()) {
                      echo '<tr>
                      	<td class="cCentrato"> '. $dati2['NrOrigine'].'</td>
                        <td class="cCentrato"> 0</td>
                        <td class="cCentrato"> 0</td>
                        <td class="cCentrato"> 0 </td>
                      	<td class="cCentrato"> '. round($dati2['TotaleVenditac'],2).'</td>
                        <td class="cCentrato"> '. round($dati2['TotaleVenditac'],2).'</td>
                      </tr>';
                  }
              echo " </tbody>
              </table>";
    }
  ?>
