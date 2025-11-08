<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Share Certificate</title>
<style>
  body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 10px;
  }
   .header-line {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    margin-bottom: 5px;
  }
  .underline {
    display: inline-block;
    border-bottom: 1px solid #000;
    width: 40px;
    text-align: center;
  }
  .title {
    text-align: center;
    font-weight: bold;
    font-size: 18px;
    color: #a00;
    text-decoration: underline;
    margin: 5px 0;
  }
  .society-name {
    text-align: center;
    font-weight: bold;
    font-size: 13px;
  }
  .society-address {
    text-align: center;
    font-size: 11px;
  }
  .section {
    margin: 10px 0;
    line-height: 1.4;
  }
      .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 130px;
        color: rgba(0, 0, 0, 0.1);
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
        z-index: -1;
        width: 100%;
        text-align: center;
        pointer-events: none;
    }
</style>
</head>
<body>
<table style="width: 100%;">
  <tr>
    <td style="width: 50%; border: 1px solid #000; padding: 10px;">
            @include('livewire.menus.partials.certificate-template', ['details' => $details])
    </td>
    <td style="width: 50%; border: 1px solid #000; padding: 10px;">
      @include('livewire.menus.partials.certificate-template', ['details' => $details])
    </td>
  </tr>
</table>
<div class="received">
  Received share certificate<br>
  Name <span class="underline" style="width:100px;"></span>
  Sign <span class="underline" style="width:80px;"></span>
  Date <span class="underline" style="width:60px;"></span>
</div>
</body>
</html>
