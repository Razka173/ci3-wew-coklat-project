<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title ?></title>
	<style type="text/css" media="print">
		body {
			font-family: Arial;
			font-size: 12px;
		}
		.cetak {
			width: 19cm;
			height: 27cm;
			padding: 1cm;
		}
		table {
			border: solid thin #000;
			border-collapse: collapse;
		}
		td td {
			padding: 3mm 6mm;
			text-align: left;
			vertical-align: top;
		}
		th {
			background-color: #f5f5f5;
			font-weight: bold;
		}
		h1 {
			text-align: center;
			font-size: 18px;
			text-transform: uppercase;
		}
	</style>
	<style type="text/css" media="screen">
		body {
			font-family: Arial;
			font-size: 12px;
		}
		.cetak {
			width: 19cm;
			height: 27cm;
			padding: 1cm;
		}
		table {
			border: solid thin #000;
			border-collapse: collapse;
		}
		td td {
			padding: 3mm 6mm;
			text-align: left;
			vertical-align: top;
		}
		th {
			background-color: #f5f5f5;
			font-weight: bold;
		}
		h1 {
			text-align: center;
			font-size: 18px;
			text-transform: uppercase;
		}
	</style>
</head>
<body onload="print()">
	<div class="cetak">
		<h1>DETAIL TRANSAKSI <?php echo $site->namaweb ?></h1>
		<table class="table table-bordered">
		<thead>
			<tr>
				<th width="25%">NAMA PELANGGAN</th>
				<th>: <?php echo $header_transaksi->nama_pelanggan ?></th>
			</tr>
			<tr>
				<th width="25%">KODE TRANSAKSI</th>
				<th>: <?php echo $header_transaksi->kode_transaksi ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Tanggal</td>
				<td>: <?php echo date('d-m-Y',strtotime($header_transaksi->tanggal_transaksi))?></td>
			</tr>
			<tr>
				<td>Jumlah Total</td>
				<td>: <?php echo number_format($header_transaksi->jumlah_transaksi) ?></td>
			</tr>
			<tr>
				<td>Status Bayar</td>
				<td>: <?php echo $header_transaksi->status_bayar ?></td>
			</tr>
			<tr>
				<td>Bukti Bayar</td>
				<td>: <?php if( $header_transaksi->bukti_bayar == "") {echo 'Belum ada';}else{ 
				?>
					<img src="<?php echo base_url('assets/upload/image/'.$header_transaksi->bukti_bayar) ?>" class="img img-thumbnail" width="200">
				<?php } ?>
				</td>
			</tr>
			<tr>
				<td>Tanggal Bayar</td>
				<td>: <?php echo date('d-m-Y', strtotime($header_transaksi->tanggal_bayar)) ?></td>
			</tr>
			<tr>
				<td>Jumlah Bayar</td>
				<td>: Rp. <?php echo number_format($header_transaksi->jumlah_bayar, '0',',','.') ?></td>
			</tr>
			<tr>
				<td>Pembayaran dari</td>
				<td>: <?php echo $header_transaksi->nama_bank ?> No. Rekening <?php echo $header_transaksi->rekening_pembayaran ?> a.n <?php echo $header_transaksi->rekening_pelanggan ?></td>
			</tr>
			<tr>
				<td>Pembayaran ke rekening</td>
				<td>: <?php echo $header_transaksi->bank ?> No. Rekening <?php echo $header_transaksi->nomor_rekening ?> a.n <?php echo $header_transaksi->nama_pemilik ?></td>
			</tr>
		</tbody>
		</table>

		<hr>

		<table class="table table-bordered" width="100%">
		<thead>
			<tr class="bg-success">
				<th>NO</th>
				<th>KODE</th>
				<th>NAMA PRODUK</th>
				<th>JUMLAH</th>
				<th>HARGA</th>
				<th>SUB TOTAL</th>
			</tr>
		</thead>
		<tbody>
			<?php $i=1; foreach($transaksi as $transaksi) { ?>
			<tr>
				<td><?php echo $i ?></td>
				<td><?php echo $transaksi->kode_produk ?></td>
				<td><?php echo $transaksi->nama_produk ?></td>
				<td><?php echo number_format($transaksi->jumlah) ?></td>
				<td><?php echo number_format($transaksi->harga) ?></td>
				<td><?php echo number_format($transaksi->total_harga) ?></td>
			</tr>
			<?php $i++; } ?>
		</tbody>
		</table>
	</div>
</body>
</html>