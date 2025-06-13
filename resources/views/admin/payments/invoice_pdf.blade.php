<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Tax Invoice</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; font-size: 14px;">
    
  <section style="width: 100%; max-width: 1500px; margin: auto; box-shadow: 0 0 10px #ddd; background-color: #fff; border-radius: 10px;">
    <div>
      <h4 style="text-align: center; margin: 0; font-weight: bold;">Tax Invoice</h4>

      <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 15px;">
        <tr style="border: 1px solid #ddd; padding: 10px; background: #f8f8f8;">
          <td style="border: 1px solid #ddd; text-align: center; font-size: 14px; color: #4a4a4a;">
              <?php

                $imageUrl = public_path('logo.png');
            
                $imageData = base64_encode(file_get_contents($imageUrl));
            
                $type = 'png'; // placeholder.com returns PNGs
            
                $src = 'data:image/' . $type . ';base64,' . $imageData;
            
            ?>
                <img src="{{ $src }}" alt="RealTrust123456" style="width: 100px; height: 100px;" />

                <p style="font-weight: bold; margin-top: 15px">
                  GST TIN : 29AAOCR0147E1ZB
                </p>
                <p style="font-weight: bold">
                  Toll Free No. :
                  <a href="tel:018001236477" style="color: #00bb07"
                    >91429-50245</a
                  >
                </p>

          </td>
          <td style="border: 1px solid #ddd; text-align: right; padding: 15px; color: #323232;">
            <div>
              <h4 style="margin-top: 5px; margin-bottom: 5px;">Bill to</h4>
              <p>NAME :- {{ $invoice->user->name }}</p>
              <p>OrderID :- {{ $invoice->order_id }}</p>
              <p style="font-size: 14px;">
                MOB: <a href="tel:{{ $invoice->user->mobile_no }}" style="color: #00bb07;">{{ $invoice->user->mobile_no }}</a>
              </p>
            </div>
          </td>
        </tr>
      </table>
    </div>
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 15px;">
        <tr style=" padding: 10px; background: #fcbd021f;">
          <td style=" text-align: center; font-size: 14px; color: #4a4a4a;">
          <h3>
            <p style="font-weight: 300; font-size: 85%; color: #626262; margin-top: 7px;">
              Order ID: <span style="color: #00bb07;">{{ $invoice->order_id }}</span><br />
              <p style="margin: 5px 0;">Invoice Generated:- {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('F d, Y') }}</p><br />
            </p>
          </h3>
          </td>
          <td>
          <h4 style="margin: 0;">Sold By:</h4>
          <p>{{$property->owner->name}}, </br> {{$invoice->seller_add}}</p>
          </td>
        </tr>
      </table>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed;">
      <thead>
        <tr style="background: #fcbd021f; padding: 15px;">
          <th style="width: 5%; border: 1px solid #ddd; padding: 10px; text-align: center;">#</th>
          <th style="width: 40%; border: 1px solid #ddd; padding: 10px; text-align: center;">Property Title</th>
          <th style="width: 15%; border: 1px solid #ddd; padding: 10px; text-align: center;">Amount</th>
          <th style="width: 10%; border: 1px solid #ddd; padding: 10px; text-align: center;">GST %</th>
          <th style="width: 20%; border: 1px solid #ddd; padding: 10px; text-align: center;">TOTAL Amount</th>
          <th style="width: 10%; border: 1px solid #ddd; padding: 10px; text-align: center;">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="border: 1px solid #ddd; text-align: center; padding: 10px; font-size: 12px;">01</td>
          <td style="border: 1px solid #ddd; text-align: center; padding: 10px; font-size: 12px;">{{ $property->title }}</td>
          <td style="border: 1px solid #ddd; text-align: center; padding: 10px; font-size: 12px;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($invoice->amount) }}</td>
          <td style="border: 1px solid #ddd; text-align: center; padding: 10px; font-size: 12px;">{{ $invoice->gst_percent }}</td>
          <td style="border: 1px solid #ddd; text-align: center; padding: 10px; font-size: 12px;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($invoice->total_amount) }}</td>
          <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
            @if($invoice->status == 1)
            <span style="background-color: green; color: white; font-size: 11px; padding: 2px; border-radius: 4px;">Approved</span>
            @else
            <span style="background-color: red; color: white; font-size: 11px; padding: 2px; border-radius: 4px;">Pending</span>
            @endif
          </td>
        </tr>
      </tbody>
    </table>
    <table style="width: 100%; border-collapse: collapse; margin-top: 30px;">
      <tr style="background: #fcbd02;">
        <th style="border: 1px solid #ddd; padding: 10px;">Total Amount</th>
        <td style="border: 1px solid #ddd; padding: 10px; text-align: right;"><b><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($invoice->total_amount) }}</b></td>
      </tr>
    </table>
    <table style="width: 100%; margin-top: 30px;">
      <tr>
        <td>
          <h4 style="margin: 10px 0;">Whether tax is Payable under reverse charge - No</h4>
          <p>This is computer generated invoice and hence signature is not required</p>
        </td>
      </tr>
    </table>
  </section>
</body>

</html>
