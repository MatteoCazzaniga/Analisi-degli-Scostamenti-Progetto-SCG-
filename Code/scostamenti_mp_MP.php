<?php
include_once("intestazione.php");
include_once("connessione.php");
    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    
	$sql1="select c.CodiceMP, c.ImportoCostoTotale as ImportoCostoTotalec, ((b.ImportoCostoTotale)/(b.QuantitaMPImpiegata)*(c.QuantitaMPImpiegata)) as MixEff ,b.ImportoCostoTotale as ImportoCostoTotaleb
from (select CodiceMP, sum(QuantitaMPImpiegata) as QuantitaMPImpiegata, sum(ImportoCostoTotale) as ImportoCostoTotale
from consumi 
where BudgetConsuntivo='CONSUNTIVO'
GROUP BY CodiceMP) as c join (select CodiceMP, sum(QuantitaMPImpiegata) as QuantitaMPImpiegata, sum(ImportoCostoTotale) as ImportoCostoTotale
from consumi 
where BudgetConsuntivo='BUDGET'
GROUP BY CodiceMP) as b on c.CodiceMP=b.CodiceMP
order by c.CodiceMP;";
    $result1= $conn->query($sql1);
    
    $sql2="select CodiceMP, sum(ImportoCostoTotale) as ImportoCostoTotalec
from consumi
where BudgetConsuntivo='CONSUNTIVO' and CodiceMP not in (select CodiceMP from consumi where BudgetConsuntivo='BUDGET' group by CodiceMP)
group by CodiceMP  
ORDER BY `consumi`.`CodiceMP` ASC;";
    $result2=$conn->query($sql2);
    
    $sql3="select CodiceMP, sum(ImportoCostoTotale) as ImportoCostoTotaleb
from consumi
where BudgetConsuntivo='BUDGET' and CodiceMP not in (select CodiceMP from consumi where BudgetConsuntivo='CONSUNTIVO' group by CodiceMP)
group by CodiceMP  
ORDER BY `consumi`.`CodiceMP` ASC;";
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
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="scostamenti_mp_articolo.php">Scostamento MP per Articolo</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#">Scostamento MP per Materia Prima</a></li>
                </ul>
            </div>
        </nav>';
        echo"<table class='table'>
                  <thead class='thead-dark'>
                      <tr>
                        <th scope='col'> NR MP</th>
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
                      	<td class="cCentrato"> '. $dati['CodiceMP'].'</td>
                      	<td class="cCentrato"> '. round($dati['ImportoCostoTotaleb'],2).'</td>
                        <td class="cCentrato"> '. round($dati['MixEff']-$dati['ImportoCostoTotaleb'],2).'</td>
						<td class="cCentrato"> '. round($dati['MixEff'],2).'</td>
                        <td class="cCentrato"> '. round($dati['ImportoCostoTotalec']-$dati['MixEff'],2).'</td>
                        <td class="cCentrato"> '. round($dati['ImportoCostoTotalec'],2).'</td>
                      </tr>';
                  }
                   while($dati3 = $result3->fetch_assoc()) {
                      echo '<tr>
                      	<td class="cCentrato"> '. $dati3['CodiceMP'].'</td>
                      	<td class="cCentrato"> '. round($dati3['ImportoCostoTotaleb'],2).'</td>
                        <td class="cCentrato"> '. round(-$dati3['ImportoCostoTotaleb'],2).'</td>
						<td class="cCentrato"> 0 </td>
                        <td class="cCentrato"> 0</td>
                        <td class="cCentrato"> 0 </td>
                      </tr>';
                  }
                  while($dati2 = $result2->fetch_assoc()) {
                      echo '<tr>
                      	<td class="cCentrato"> '. $dati2['CodiceMP'].'</td>
                      	<td class="cCentrato"> 0 </td>
                        <td class="cCentrato"> 0 </td>
						<td class="cCentrato"> 0 </td>
                        <td class="cCentrato"> '. round($dati2['ImportoCostoTotalec'],2).'</td>
                        <td class="cCentrato"> '. round($dati2['ImportoCostoTotalec'],2).'</td>
                      </tr>';
                  }
              echo " </tbody>
              </table>";
    }
  ?>