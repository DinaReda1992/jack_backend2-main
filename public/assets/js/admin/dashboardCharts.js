$(function (){
    //- LINE CHART -
    //--------------

    var randomScalingFactor = function() {
        return Math.round(Math.random() * 100);
    };

    var configPio = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                ],
                backgroundColor: [
                    "#00a65a",
                    "#eaed28",
                    "#FF9800",
                    "#dd4b39",
                ],
                label: 'Dataset 1'
            }],
            labels: [
                'Low Risk',
                'Moderate Risk',
                'High Risk',
                'Extreme Risk',
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'left',
            },

            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };

    // window.onload = function() {
    //     var ctx = document.getElementById('pieChart').getContext('2d');
    //     window.myDoughnut = new Chart(ctx, configPio);
    // };
    // document.getElementById('changeCircleSize').addEventListener('click', function() {
    //     if (window.myDoughnut.options.circumference === Math.PI) {
    //         window.myDoughnut.options.circumference = 2 * Math.PI;
    //         window.myDoughnut.options.rotation = -Math.PI / 2;
    //     } else {
    //         window.myDoughnut.options.circumference = Math.PI;
    //         window.myDoughnut.options.rotation = -Math.PI;
    //     }
    //
    //     window.myDoughnut.update();
    // });
//


    var randomScalingFactor = function() {
        return Math.ceil(Math.random() * 10.0) * Math.pow(10, Math.ceil(Math.random() * 5));
    };
    var config = {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [
                    {
                        label: "Electronics",
                        fill: false,

                        fillColor: "rgb(210, 214, 222)",
                        strokeColor: "rgb(210, 214, 222)",
                        backgroundColor:"rgb(210, 214, 222)",
                        borderColor:"rgb(210, 214, 222)",
                        pointColor: "rgb(210, 214, 222)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgb(220,220,220)",
                        data: [65, 59, 80, 81, 56, 55, 40]
                    },
                    {
                        label: "sfsdf",
                        fill: false,

                        fillColor: "rgba(103,58,183,0.9)",
                        strokeColor: "rgba(103,58,183,0.8)",
                        pointColor: "#764dbe",
                        pointStrokeColor: "rgba(103,58,183,1)",
                        backgroundColor:"rgba(103,58,183,1)",
                        borderColor:"rgba(103,58,183,1)",

                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(103,58,183,1)",
                        data: [80, 70, 60, 10, 50, 30, 100]
                    },
                    {
                        label: "Digital Goods",
                        fill: false,

                        fillColor: "rgba(60,141,188,0.9)",
                        strokeColor: "rgba(60,141,188,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(60,141,188,1)",
                        backgroundColor:"rgba(60,141,188,1)",
                        borderColor:"rgba(60,141,188,1)",

                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [28, 48, 40, 19, 86, 27, 90]
                    }

                ]
        },
        options: {
            responsive: true,

            scales: {
                x: {
                    display: true,
                },
                y: {
                    display: true,
                    type: 'logarithmic',
                }
            }
        }
    };
    var ctx = document.getElementById('salesChart').getContext('2d');
    window.myLine = new Chart(ctx, config);

    document.getElementById('changeToBar').addEventListener('click', function() {
        if (window.myLine.config.type==="line") {
            window.myLine.config.type="bar";
        }
        window.myLine.update();
        $("#changeToBar").addClass("active");
        $("#changeToLine").removeClass("active");

    });
    document.getElementById('changeToLine').addEventListener('click', function() {
        if (window.myLine.config.type==="bar") {
            window.myLine.config.type="line";
        }
        window.myLine.update();
        $("#changeToLine").addClass("active");
        $("#changeToBar").removeClass("active");

    });

    $('#what_reported').multiselect({
        onChange: function(option, checked) {
            // Get selected options.
            var selectedOptions = $('#what_reported option:selected');
        },nonSelectedText: 'What Reported ?'
    });
    $('.example-getting-started').multiselect({
        onChange: function(option, checked) {
            // Get selected options.
            var selectedOptions = $('.example-getting-started option:selected');
        },nonSelectedText: 'Choose what appear'
    });
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}
    });

    $("#applyChart").click(function (){
        var formData = new FormData();
        var startDate=  $("#reportrange").data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate=  $("#reportrange").data('daterangepicker').endDate.format('YYYY-MM-DD');



        formData.append('startDate', startDate);
        formData.append('endDate', endDate);

        $.ajax({
            url:"/polyclinic-panel/getOvrChart",
            processData: false,
            contentType: false,
            type: 'POST',
            data:formData,
            success:function(){
            }
        })
    })
});



