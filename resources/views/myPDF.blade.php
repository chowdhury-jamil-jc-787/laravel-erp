<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .terms {
            font-size: 12px;
            margin-top: 20px;
        }

        .signature {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .footer {
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 15px;
            text-align: center;
        }

        .footer p {
            margin: 0;
        }

        .company-info {
            font-size: 12px;
            margin-top: 15px;
        }

        .company-info span {
            display: block;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            margin-top: 5px;
        }

        .contact-info div {
            margin: 0 15px;
        }

        .footer a {
            color: blue;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <!-- Header Section -->
        <div class="row">
            <div class="col-md-6">
                <h2 style="color:darkblue">Ampec Technologies Pty Ltd</h2><br><br>
                <p>Attn:<br><strong>John Pansini</strong><br>Smoke Control</p>
                <p><strong>Quote No: </strong>working_20240823_RH2</p>
            </div>
            <div class="col-md-6 text-end">
    <img src="{{ asset('asset/img/a.jpg') }}" alt="Logo" style="max-height: 80px;">
    <br><br>
    <p><strong>Date:</strong> 12/09/2024</p>
</div>

        </div>

        <!-- Table Section -->
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Cust P/N</th>
                    <th>Ampec P/N</th>
                    <th>Desc</th>
                    <th>QTY (Pcs)</th>
                    <th>U/P (AUD, ex GST)</th>
                    <th>L/T</th>
                    <th>NCNR?</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>RM-CS-CR68</td>
                    <td>DCA55842</td>
                    <td>Slave Terminator</td>
                    <td>30</td>
                    <td>$36.85</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RM-CS-CR68</td>
                    <td>DCA55842</td>
                    <td>Slave Terminator</td>
                    <td>60</td>
                    <td>$35.75</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RM-CS-CR68</td>
                    <td>DCA55842</td>
                    <td>Slave Terminator</td>
                    <td>100</td>
                    <td>$34.80</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RM-CS-CR196</td>
                    <td>DCA55843</td>
                    <td>Slave Comm Cable</td>
                    <td>30</td>
                    <td>$142.55</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RM-CS-CR196</td>
                    <td>DCA55843</td>
                    <td>Slave Comm Cable</td>
                    <td>60</td>
                    <td>$137.10</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RM-CS-CR196</td>
                    <td>DCA55843</td>
                    <td>Slave Comm Cable</td>
                    <td>100</td>
                    <td>$134.95</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RM-CS-CR200</td>
                    <td>DCA55844</td>
                    <td>Mains Power Lead</td>
                    <td>30</td>
                    <td>$44.95</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td>CB-PS-89 Will be used as Mains cable. Datasheet attached.</td>
                </tr>
                <tr>
                    <td>RM-CS-CR200</td>
                    <td>DCA55844</td>
                    <td>Mains Power Lead</td>
                    <td>60</td>
                    <td>$42.80</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td>CB-PS-89 Will be used as Mains cable. Datasheet attached.</td>
                </tr>
                <tr>
                    <td>RM-CS-CR200</td>
                    <td>DCA55844</td>
                    <td>Mains Power Lead</td>
                    <td>100</td>
                    <td>$41.40</td>
                    <td>4-6 Weeks</td>
                    <td>Yes</td>
                    <td>CB-PS-89 Will be used as Mains cable. Datasheet attached.</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-end"><strong>Freight & Handling</strong></td>
                    <td>1<br>$18.50<br>Up to 15 Kgs</td>
                </tr>
            </tbody>
        </table>

        <!-- Terms and Conditions -->
        <div class="terms">
            <p><strong>Terms and Conditions of Sale</strong></p>
            <ol>
                <li>Ampec Technologies Pty Ltd Standard Terms & Conditions of Sale apply to this quote. Available upon request.</li>
                <li>Order quantities must be in multiples of Standard pack size or over the MOQ whichever is greater.</li>
                <li>Products quoted “ex-stock” are at time of quote only and are subject to prior sales.</li>
                <li>This quotation is valid for 30 days from the date of issue, then subject to confirmation.</li>
                <li>All prices GST exclusive.</li>
            </ol>
            <p>If you have any queries, please feel free to contact us.</p>
        </div>

<!-- Signature Section -->
<div class="signature">
    <p>Best Regards,<br><strong>Shane Waz</strong><br>Key Account Manager</p>
    <img src="{{ asset('asset/img/a.jpg') }}" alt="Logo" style="max-height: 40px;"><br>
    <strong>Ampec Technologies Pty Ltd</strong><br>
    Unit 1 63-79 Parramatta Road<br>
    Silverwater NSW 2128<br>
    Australia<br>
    Tel: +612 – 8741 5089<br>
    Ph: +612 – 8741 5000<br>
    Fax: (02) 9684 4500<br>
    Email: shane@ampec.com.au<br>
    Web: <a href="http://www.ampec.com.au">www.ampec.com.au</a>
</div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="company-info">
                <span><strong>Unit 1, 63-79 Parramatta Road, Silverwater, NSW 2128, Australia</strong></span>
            </div>
            <div class="contact-info">
                <div>Tel: (02) 8741 5000</div>
                <div>Fax: (02) 9648 4500</div>
                <div>Email: <a href="mailto:info@ampec.com.au">info@ampec.com.au</a></div>
                <div>Web: <a href="http://www.ampec.com.au">www.ampec.com.au</a></div>
            </div>
        </div>
    </div>
</body>

</html>
