<?php
include_once("connessione.php");
    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                RICAVI                                               //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //QTA BUDGET (vendita) 6948 - 395337.55
    $ricaviB="SELECT sum(v.quantita), sum(v.TotaleVendita/t.TassoDiCambioMedio)
      FROM vendite as v
      JOIN Clienti as c ON (v.NrOrigine = c.Nr)
        JOIN tassidicambio as t ON (c.valuta = t.CodiceValuta)
      WHERE v.BudgetConsuntivo = 'BUDGET' AND v.BudgetConsuntivo=t.Anno";

    //QTA CONSUNTIVO (vendita) 8963 - 507842.85
    $ricaviC="SELECT sum(v.quantita), sum(v.TotaleVendita/t.TassoDiCambioMedio)
      FROM vendite as v
      JOIN Clienti as c ON (v.NrOrigine = c.Nr)
        JOIN tassidicambio as t ON (c.valuta = t.CodiceValuta)
      WHERE v.BudgetConsuntivo = 'CONSUNTIVO' AND v.BudgetConsuntivo=t.Anno";

    //509989.49
    $MIXSTDRICAVI = "SELECT sum((v.quantita)/(SELECT sum(v2.quantita) FROM vendite as v2 WHERE v2.BudgetConsuntivo='BUDGET')*(v.TotaleVendita/t.TassoDiCambioMedio/v.quantita)*((SELECT sum(v3.quantita) FROM vendite as v3 WHERE v3.BudgetConsuntivo='CONSUNTIVO')))
      FROM vendite as v
      JOIN Clienti as c ON (v.NrOrigine = c.Nr)
        JOIN tassidicambio as t ON (c.valuta = t.CodiceValuta)
      WHERE v.BudgetConsuntivo = 'BUDGET' AND v.BudgetConsuntivo=t.Anno";

    //547413.77
    $MIXEFFRICAVI = "SELECT sum((vb.TotaleVendita/vb.quantita)*vc.quantita)
    from vendite_corrette_c as vc join vendite_corrette_b as vb on vc.NrArticolo=vb.NrArticolo";


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                MD                                                   //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    //QTA BUDGET (consumi) 144672
    $MDB = "SELECT sum(QMP), sum(CostoMP)
            FROM costo_mp_b";

    //QTA CONSUNTIVO (consumi) 211436
    $MDC = "SELECT sum(QMP), sum(CostoMP)
            FROM costo_mp_c";

    //318978.08
    $MIXSTDMD = "SELECT sum((cc.CostoMP/cib.QuantitaTot)*(cib.QuantitaTot/(select sum(QuantitaTot)
    from costo_impiego_b_unit_vero))*(select sum(QuantitaTot) from costo_impiego_c_unit_vero))
                from costo_mp_b as cc join costo_impiego_c_unit_vero as cic on cc.NrArticolo=cic.NrArticolo
                join costo_impiego_b_unit_vero as cib on cc.NrArticolo=cib.NrArticolo;";

    //294100.70
    $MIXEFFMD = "SELECT sum((cc.CostoMP/cib.QuantitaTot)*cic.QuantitaTot)
from costo_mp_b as cc join costo_impiego_c_unit_vero as cic on cc.NrArticolo=cic.NrArticolo join costo_impiego_b_unit_vero as cib on cc.NrArticolo=cib.NrArticolo;";

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                LD                                                   //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    //QTA BUDGET (ImpiegoOrarioRisorse) 7711  - 198729.00
    $LDB = "SELECT sum(QuantitaTot), sum(CostoTot) FROM `costo_impiego_b_unit_vero`;";

    //QTA CONSUNTIVO (ImpiegoOrarioRisorse) 12797  -  156142.97
    $LDC = "SELECT sum(QuantitaTot), sum(CostoTot) FROM costo_impiego_c_unit_vero ";

    //329583.61
    $MIXSTDLD = "SELECT sum(QuantitaTot/(SELECT sum(QuantitaTot)FROM costo_impiego_b_unit_vero)*(SELECT sum(QuantitaTot) from costo_impiego_c_unit_vero)*(CostoTot/QuantitaTot)) FROM `costo_impiego_b_unit_vero`;";

    //363437.96
    $MIXEFFLD = "SELECT sum((cib.CostoTot/cib.QuantitaTot)*cic.QuantitaTot)
from costo_mp_b as cc join costo_impiego_c_unit_vero as cic on cc.NrArticolo=cic.NrArticolo join costo_impiego_b_unit_vero as cib on cc.NrArticolo=cib.NrArticolo;";


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                QUERY                                                //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
      $RBUDGET = $conn->query($ricaviB);
      $RCONSUNTIVO = $conn->query($ricaviC);
      $MDBUDGET = $conn->query($MDB);
      $MDCONSUNTIVO = $conn->query($MDC);
      $LDBUDGET = $conn->query($LDB);
      $LDCONSUNTIVO = $conn->query($LDC);
      $MIXRICAVISTD = $conn->query($MIXSTDRICAVI);
      $MIXMDSTD = $conn->query($MIXSTDMD);
      $MIXLDSTD = $conn->query($MIXSTDLD);
      $MIXRICAVIEFF = $conn->query($MIXEFFRICAVI);
      $MIXMDEFF = $conn->query($MIXEFFMD);
      $MIXLDEFF = $conn->query($MIXEFFLD);




    //END Connection
    $conn->close();

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                RICAVI                                               //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //converto in int la qta e double il totale in euro
    $RBUDGET = $RBUDGET->fetch_array();
    $ricaviBUDGET = intval($RBUDGET[0]);
    $ricaviBUDGETE = doubleval($RBUDGET[1]);

    //converto in int la qta e double il totale in euro
    $RCONSUNTIVO = $RCONSUNTIVO->fetch_array();
    $ricaviCONSUNTIVO = intval($RCONSUNTIVO[0]);
    $ricaviCONSUNTIVOE = doubleval($RCONSUNTIVO[1]);

    //converto in double STD
    $TOTRICAVIMIXSTD = $MIXRICAVISTD->fetch_array();
    $MIXSTDRICAVI = doubleval($TOTRICAVIMIXSTD[0]);

    //converto in double EFF
    $TOTRICAVIMIXEFF = $MIXRICAVIEFF->fetch_array();
    $MIXEFFRICAVI = doubleval($TOTRICAVIMIXEFF[0]);

    //MIX
    $ricaviMS = round($MIXSTDRICAVI,2);
    $ricaviME = round($MIXEFFRICAVI,2);


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                MD                                                   //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //converto in int la qta e double il totale in euro
    $MDBUDGET = $MDBUDGET->fetch_array();
    $materialedirettoBUDGET = intval($MDBUDGET[0]);
    $materialedirettoBUDGETE = doubleval($MDBUDGET[1]);

    //converto in int la qta e double il totale in euro
    $MDCONSUNTIVO = $MDCONSUNTIVO->fetch_array();
    $materialedirettoCONSUNTIVO = intval($MDCONSUNTIVO[0]);
    $materialedirettoCONSUNTIVOE = doubleval($MDCONSUNTIVO[1]);


    //converto in double STD
    $TOTMDMIXSTD = $MIXMDSTD->fetch_array();
    $MIXSTDMD = doubleval($TOTMDMIXSTD[0]);

    //converto in double EFF
    $TOTMDMIXEFF = $MIXMDEFF->fetch_array();
    $MIXEFFMD = doubleval($TOTMDMIXEFF[0]);

    //MIX
    $materialedirettoMS = round($MIXSTDMD,2);
    $materialedirettoME = round($MIXEFFMD,2);


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                LD                                                   //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //converto in int la qta e double il totale in euro
    $LDBUDGET = $LDBUDGET->fetch_array();
    $lavorodirettoBUDGET = intval($LDBUDGET[0]);
    $lavorodirettoBUDGETE = doubleval($LDBUDGET[1]);

    //converto in int la qta e double il totale in euro
    $LDCONSUNTIVO = $LDCONSUNTIVO->fetch_array();
    $lavorodirettoCONSUNTIVO = intval($LDCONSUNTIVO[0]);
    $lavorodirettoCONSUNTIVOE = doubleval($LDCONSUNTIVO[1]);

    //converto in double STD
    $TOTLDMIXSTD = $MIXLDSTD->fetch_array();
    $MIXSTDLD = doubleval($TOTLDMIXSTD[0]);

    //converto in double EFF
    $TOTLDMIXEFF = $MIXLDEFF->fetch_array();
    $MIXEFFLD = doubleval($TOTLDMIXEFF[0]);

    //MIX
    $lavorodirettoMS = round($MIXSTDLD,2);
    $lavorodirettoME = round($MIXEFFLD,2);



    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                              COSTI TOTALI                                           //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    $costitotaliBUDGET = $materialedirettoBUDGETE + $lavorodirettoBUDGETE;
    $costitotaliCONSUNTIVO = $materialedirettoCONSUNTIVOE + $lavorodirettoCONSUNTIVOE;

    $costitotaliMS =  $materialedirettoMS + $lavorodirettoMS;
    $costitotaliME =  $materialedirettoME + $lavorodirettoME;


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                                                SCOSTAMENTI                                          //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    $scostamentoRICAVI = $ricaviBUDGET - $ricaviCONSUNTIVO;
    $scostamentoRICAVIE = $ricaviBUDGETE - $ricaviCONSUNTIVOE;
    $scostamentoMATERIALEDIRETTO = $materialedirettoBUDGET - $materialedirettoCONSUNTIVO;
    $scostamentoMATERIALEDIRETTOE = $materialedirettoBUDGETE - $materialedirettoCONSUNTIVOE;
    $scostamentoLAVORODIRETTO = $lavorodirettoBUDGET - $lavorodirettoCONSUNTIVO;
    $scostamentoLAVORODIRETTOE = $lavorodirettoBUDGETE - $lavorodirettoCONSUNTIVOE;
?>