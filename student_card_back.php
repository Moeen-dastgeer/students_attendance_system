<?php
include 'admin_header.php';
$card_count = $_GET['count'] ?? 12;
?>

<style>
  * {
    box-sizing: border-box;
  }
  body {
    font-family: Arial, sans-serif;
  }
  .container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .id-card-back {
    width: 4.5cm;
    height: 8cm;
    border: 1px solid #333;
    padding: 3px;
    font-size: 10px;
    background-color: #fdfdfd;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
  }
  .institute {
    font-weight: bold;
    font-size: 11px;
  }
  .logo {
    width: 2.5cm;
    height: 2.5cm;
    margin: 10px auto;
  }
  .logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .address {
    margin: 10px 0;
  }
  .note {
    margin-top: auto;
    font-size: 9px;
  }
  .authority {
    text-align: right;
    font-size: 9px;
    font-weight: bold;
    margin-top: 10px;
  }
  .page-break {
    page-break-after: always;
  }
  @media print {
    .no-print {
      display: none;
    }
  }
</style>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <button onclick="window.print()" class="btn btn-primary no-print">🖨️ Print</button>
    </div>
  </div>

  <?php
  $cards_per_page = 20;
  $full_pages = floor($card_count / $cards_per_page);
  $remaining = $card_count % $cards_per_page;

  for ($p = 0; $p < $full_pages; $p++): ?>
    <div class="container page-break">
      <?php for ($i = 1; $i <= $cards_per_page; $i++): ?>
        <div class="id-card-back">
          <div class="institute">Fatima Institute Of Computer Education<br>And Resources</div>
          <div class="logo"><img src="ficer.jpg" alt="Logo"></div>
          <div class="address">
            Faisalabad Road Okara<br>
            Contact No: 044-2661147
          </div>
          <div class="note">
            In the case of loss of this card, kindly return it to<br>the above address.
          </div>
          <div class="authority">Issuing Authority</div>
        </div>
      <?php endfor; ?>
    </div>
  <?php endfor; ?>

  <?php if ($remaining > 0): ?>
    <div class="container">
      <?php for ($i = 1; $i <= $remaining; $i++): ?>
        <div class="id-card-back">
          <div class="institute">Fatima Institute Of Computer Education<br>And Resources</div>
          <div class="logo"><img src="ficer.jpg" alt="Logo"></div>
          <div class="address">
            Faisalabad Road Okara<br>
            Contact No: 044-2661147
          </div>
          <div class="note">
            In the case of loss of this card, kindly return it to<br>the above address.
          </div>
          <div class="authority">Issuing Authority</div>
        </div>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>

<?php include 'admin_footer.php'; ?>
