<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Credit Card Payment Form Template | PrepBootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" type="text/css" href="/site/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/site/font-awesome/css/font-awesome.min.css" />

    <script type="text/javascript" src="/site/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/site/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">

{{--    <div class="page-header">--}}
{{--        <h1>Credit Card Payment Form <small>A responsive credit card payment template</small></h1>--}}
{{--    </div>--}}
{{--{{isset($results)?$results:''}}--}}
    <!-- Credit Card Payment Form - START -->

    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-md-4 col-md-offset-4" style="margin-top: 20px;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <h3 class="text-center">Payment Details</h3>
                            <img class="img-responsive cc-img" src="http://www.prepbootstrap.com/Content/images/shared/misc/creditcardicons.png">
                        </div>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="/initiate-payment" method="POST">
                            {!! csrf_field() !!}

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>CARD NUMBER</label>
                                        <div class="input-group">
                                            <input name="card_number" value="5105105105105100" type="tel" class="form-control" placeholder="Valid Card Number" required/>
                                            <span class="input-group-addon"><span class="fa fa-credit-card"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-4 col-md-4">
                                    <div class="form-group">
                                        <label><span class="hidden-xs">Expriy Month</span><span class="visible-xs-inline">EXP</span> DATE</label>
                                        <input name="expiry_month" value="12" type="tel" class="form-control" placeholder="MM" maxlength="2" minlength="2" required/>
                                    </div>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <div class="form-group">
                                        <label><span class="hidden-xs">Expiry Year</span><span class="visible-xs-inline">EXP</span> DATE</label>
                                        <input name="expiry_year" value="31" type="tel" class="form-control" placeholder="YY" maxlength="2" minlength="2" required/>
                                    </div>
                                </div>
                                <div class="col-xs-4 col-md-4">
                                    <div class="form-group">
                                        <label>CV CODE</label>
                                        <input name="cvv" value="999" type="tel" class="form-control" placeholder="CVC" maxlength="3" minlength="3" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>CARD OWNER</label>
                                        <input name="holder_name" value="Abdelkader Zayed" type="text" class="form-control" placeholder="Card Owner Names" required/>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <button type="submit" class="btn btn-warning btn-lg btn-block">Process payment</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .cc-img {
            margin: 0 auto;
        }
    </style>
    <!-- Credit Card Payment Form - END -->

</div>

</body>
</html>