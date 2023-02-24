<?php
include_once("intestazione.php");
include_once("connessione.php");
    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    
	$sql1="select c.NrAreaDiProduzione, b.Costob, (b.Costob/b.Quantitab)*c.Quantitac as Mixeff, c.Costoc
from (select  i.BudgetConsuntivo, i.NrAreaDiProduzione, sum(i.TempoRisorsa*c.CostoOrario) as Costoc, sum(i.QuantitaDiOutput) as Quantitac  from ImpiegoOrarioRisorse as i join CostoOrarioRisorseConsuntivo as c on i.NrAreaDiProduzione=c.AreaDiProduzione and i.Risorsa=c.Risorsa where BudgetConsuntivo='CONSUNTIVO' GROUP by i.NrAreaDiProduzione
ORDER BY i.NrAreaDiProduzione)as c join (select  i.BudgetConsuntivo, i.NrAreaDiProduzione, sum(i.TempoRisorsa*c.CostoOrario) as Costob, sum(i.QuantitaDiOutput) as Quantitab  from ImpiegoOrarioRisorse as i join CostoOrarioRisorseBudget as c on i.NrAreaDiProduzione=c.AreaDiProduzione and i.Risorsa=c.Risorsa where BudgetConsuntivo='BUDGET' GROUP by i.NrAreaDiProduzione
ORDER BY i.NrAreaDiProduzione) as b on b.NrAreaDiProduzione=c.NrAreaDiProduzione;";
    $result1= $conn->query($sql1);
    
    $sql2="select i.NrAreaDiProduzione, sum(i.TempoRisorsa*c.CostoOrario) as Costoc
from ImpiegoOrarioRisorse as i join CostoOrarioRisorseConsuntivo as c on i.NrAreaDiProduzione=c.AreaDiProduzione and i.Risorsa=c.Risorsa
WHERE i.NrAreaDiProduzione='A12';";
	$result2= $conn->query($sql2);

    
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
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#">Scostamento LD per Articolo</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="scostamenti_ld_AreaProd.php">Scostamento MP per Materia Prima</a></li>
                </ul>
            </div>
        </nav>';
        echo"<table class='table'>
                  <thead class='thead-dark'>
                      <tr>
                        <th scope='col'> NrArea Produzione</th>
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
                      	<td class="cCentrato"> '. $dati['NrAreaDiProduzione'].'</td>
                      	<td class="cCentrato"> '. round($dati['Costob'],2).'</td>
                        <td class="cCentrato"> '. round($dati['Mixeff']-$dati['Costob'],2).'</td>
						<td class="cCentrato"> '. round($dati['Mixeff'],2).'</td>
                        <td class="cCentrato"> '. round($dati['Costoc']-$dati['Mixeff'],2).'</td>
                        <td class="cCentrato"> '. round($dati['Costoc'],2).'</td>
                      </tr>';
                  }
                  $dati2= $result2->fetch_assoc();
                  echo '<tr>
                      	<td class="cCentrato">'. $dati2['NrAreaDiProduzione'].'</td>
                      	<td class="cCentrato"> 0</td>
                        <td class="cCentrato"> 0 </td>
						<td class="cCentrato"> 0 </td>
                        <td class="cCentrato"> '. round($dati2['Costoc'],2).'</td>
                        <td class="cCentrato"> '. round($dati2['Costoc'],2).'</td>
                      </tr>';
              echo " </tbody>
              </table>";
    }
  ?>
