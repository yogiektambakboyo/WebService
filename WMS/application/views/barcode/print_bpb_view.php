<html>
    <head>
        <style>
                table.thinborder {
                        border-spacing:0;
                        border-collapse: collapse;
                        table-layout:fixed;
                        width:210mm;
                        padding:0px;
                }

                td.sidecol {
                        margin: 0;
                        border-width: 0 0 1px 0;
                        border-style: solid;
                }
                td.midcol {
                        margin: 0;
                        border-width: 0 1px 1px 1px;
                        border-style: solid;
                }
                p {
                        font-weight:bold;
                }
                .break { 
                        page-break-after: always; 
                }
        </style>
    </head>
    <body>
        <table class="thinborder">
            <col style="width:5mm"/>
            <col style="width:63mm"/>
            <col style="width:68mm"/>
            <col style="width:63mm"/>
            <col style="width:5mm"/>
            <?php
            //credit card size: 8.5 x 5.5 cm
            //8+1/2 in x 11 in or 215.9 mm x 279.4 mm
            //A4 (210 x 297 mm)


            try {
                $i = 1;
                $j = 0;
                while ($i <= $jmlPrint) {
                    ?>
                    <?php
                    if ($j % 3 == 0) {
                        ?>
                        <tr style="height:5mm">
                        </tr>
                        <tr style="height:50mm">
                            <td>
                            </td>
                            <?php
                        }
                        ?>

                        <?php
                        if ($j % 3 == 1) {
                            ?>
                            <td align="center" class="midcol">
                                <?php
                            } else {
                                ?>
                            <td align="center" class="sidecol">
                                <?php
                            }
							
							$j++;
                            ?>
                            <p style="clear:both;"><?php printf("BPB: %s", $kodeNota); ?></p>
							<?php echo $keterangan; ?>
							<br clear="all">
                            <img src="<?php echo base_url() ?>index.php/barcode/code39?<?php printf("size=40&text=%s", $kodeNotaExplode); ?>"/>
                            <br clear="all">
                            <img src="<?php echo base_url() ?>index.php/barcode/qrcode?<?php printf("text=%d", $kodeNotaExplode); ?>"/ >
                            
                        </td>
                        <?php
                        $i++;
                        ?>
                        <?php
                        if ($j % 3 == 0 || $i > $jmlPrint) {
                            ?>
                            <td>
                            </td>
                        </tr>
                        <?php
                    }
                    if ($j % 15 == 0 && $j > 0) {
                        ?>
                    </table>
                   
                    <table class="thinborder">
                        <col style="width:5mm"/>
                        <col style="width:65mm"/>
                        <col style="width:70mm"/>
                        <col style="width:65mm"/>
                        <col style="width:5mm"/>
                        <?php
                    }
                }
            } catch (Exception $e) {
                echo "Caught exception: ", $e->getMessage(), "\n";
            }
            ?>
        </table>
    </body>
</html>	
