<?php
if ($this->session->userdata("OperatorRole") == '10/WHR/000' or $this->session->userdata('OperatorRole') == '10/WHR/999' ) {
    ?>
    <div class="navbar navbar-static-top">
        <div class="navbar-inner">
            <div class="container">
                <div class="nav-collapse">
                    <ul class="nav">
                        <li><a href="<?php echo base_url(); ?>index.php/admin/List_Summary_Outstanding"><i class="icon-home"></i> Outstanding</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Receiving<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/transaksi/tambah_transaksi_bpb">Tambah Task BPB</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/transaksi/daftar_transaksi_bpb">Daftar Task BPB</a></li>
								 <li><a href="<?php echo base_url(); ?>index.php/transaksi/receiving_berjalan">Reciving Berjalan</a></li>
                            </ul>
                        </li>
						<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Transfer Stok Masuk<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/admin_transfermasuk/tambah_admin_transfermasuk">Tambah Transfer Stok Masuk</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_transfermasuk/daftar_admin_transfermasuk">Daftar Task Transfer Masuk</a></li>
                            </ul>
                        </li>
						<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Transfer Stok Keluar<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/admin_transferkeluar/tambahpicklist">Tambah Transfer Stok Keluar</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_transferkeluar/daftar_task_picking">Daftar Task Transfer Keluar</a></li>
                            </ul>
                        </li>
						<?php if ($this->session->userdata("OperatorRole") == '10/WHR/999') { ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Print Barcode<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/barcode/bin_barcode">Bin Barcode</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/barcode/rack_barcode">Rack Barcode</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/barcode/rack_barcode3">Rack Barcode Arrow</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/barcode/bpb">Bpb Barcode</a></li>
                                <!--li><a href="<?php echo base_url(); ?>index.php/barcode/rack_barcode2">Rack Nama Barcode</a></li-->
                            </ul>
                        </li>
						<?php } ?>
						<?php //var_dump($this->session->userdata("OperatorRole")) ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Picking<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/admin_picking/tambahpicklist">Tambah PickList</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_picking/list_picking">Edit PickList</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_picking/daftar_task_picking">Daftar Task Picking</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_picking/list_barang_kembali">Cetak Barang Kembali</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Replenish<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/admin_replenish/tambah_replenish">Replenish Limit</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_replenish/tambah_task_replenish">Replenish Manual</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_replenish_gbs">Replenish GBS</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_replenish/daftar_transaksi_rpl">Daftar Task Replenish</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Return<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/admin_picklist_retur/entry">Buat PickList Retur</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_picklist_retur/list_edit">Edit PickList Retur</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_retur/tambah_transaksi_retur">Tambah Task Retur</a></li>
                                <li><a href="<?php echo base_url(); ?>index.php/admin_retur/daftar_transaksi_retur">Daftar Task Retur</a></li>
                            </ul>
                        </li>
						<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Laporan<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/admin_laporan/stok_barang">Stok Barang</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url(); ?>index.php/reset/bin">Reset Bin</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <?php echo $this->session->userdata('login_name'); ?><b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>index.php/login/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.nav-collapse -->
            </div><!-- /.container -->
        </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->

    <?php
}
?>