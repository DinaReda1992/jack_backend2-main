$(function () {

  'use strict';
//Make the dashboard widgets sortable Using jquery UI
  $(".connectedSortable").sortable({
    placeholder: "sort-highlight",
    connectWith: ".connectedSortable",
    handle: ".box-header, .nav-tabs",
    forcePlaceholderSize: true,
    zIndex: 999999
  });
  $(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");






  //jQuery UI sortable for the todo list
  $(".todo-list").sortable({
    placeholder: "sort-highlight",
    handle: ".handle",
    forcePlaceholderSize: true,
    zIndex: 999999,
    stop: function() {
      var selectedData = new Array();
      $('.todo-list>li').each(function() {
        selectedData.push($(this).attr("parent_id"));
      });
      updateOrder(selectedData);
    }
  });
  function updateOrder(data) {
    console.log(data);
    $.ajax({
      url:"/polyclinic-panel/change-sort-tasks",
      type:'post',
      data:{position:data},
      success:function(){
      }
    })
  }
$(".task_check").change(function (){
  $.ajax({
    url:"/polyclinic-panel/change-status-task",
    type:'post',
    data:"task_id=" + $(this).val() ,
    success:function(){
    }
  })
})
  $(document).on("click", ".edit_task", function (e) {

    var task= $(this).attr("data");
    task=JSON.parse(task);
    $('#task_title').val(task.task_title);
    $('#task_duration').val(task.task_duration);
    $('#task_color').val(task.task_color);
    $('#task_id').val(task.task_id);
    $('#add-new-event').css({"background-color": task.task_color, "border-color": task.task_color});
    $('#addTask').modal('show');

  })
  $(".todo-list").todolist({
    onCheck: function (ele) {
      window.console.log("The element has been checked");
      return ele;
    },
    onUncheck: function (ele) {
      window.console.log("The element has been unchecked");
      return ele;
    }
  });
  var currColor = "#3c8dbc"; //Red by default
  //Color chooser button
  var colorChooser = $("#color-chooser-btn");
  $("#color-chooser > li > a").click(function (e) {
    e.preventDefault();
    //Save color
    currColor = $(this).css("color");
    //Add color effect to button
    $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
    $('#task_color').val(currColor);
  });
  $("#openAddTask").click(function (){
    $('#task_id').val('');
    $('#task_title').val('');
    $('#task_duration').val('');

    $('#addTask').modal('show');
  });
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}
  });
  $('#add-new-event').click(saveTask);
  function saveTask() {

    var task_title = $("#task_title").val();
    var task_duration = $("#task_duration").val();
    var task_color = $("#task_color").val();
    var task_id = $("#task_id").val();

    var data = "task_title=" + task_title + "&task_duration=" + task_duration + "&task_color=" + task_color+ "&task_id=" + task_id;
    $.ajax({
      url: "/polyclinic-panel/save_task",
      type: "POST",
      data: data,
      success: function (data) {
        if(data ==-1) return alert('something error');
$("#todo-list").html(data);
        $('#addTask').modal('hide');

      },
      error: function () {
        alert("error");
      }
    });
  }

  /* ChartJS
   * -------
   * Here we will create a few charts using ChartJS
   */



  /* jVector Maps
   * ------------
   * Create a world map with markers
   */
  $('#world-map-markers').vectorMap({
    map: 'world_mill_en',
    normalizeFunction: 'polynomial',
    hoverOpacity: 0.7,
    hoverColor: false,
    backgroundColor: 'transparent',
    regionStyle: {
      initial: {
        fill: 'rgba(210, 214, 222, 1)',
        "fill-opacity": 1,
        stroke: 'none',
        "stroke-width": 0,
        "stroke-opacity": 1
      },
      hover: {
        "fill-opacity": 0.7,
        cursor: 'pointer'
      },
      selected: {
        fill: 'yellow'
      },
      selectedHover: {
      }
    },
    markerStyle: {
      initial: {
        fill: '#00a65a',
        stroke: '#111'
      }
    },
    markers: [
      {latLng: [41.90, 12.45], name: 'Vatican City'},
      {latLng: [43.73, 7.41], name: 'Monaco'},
      {latLng: [-0.52, 166.93], name: 'Nauru'},
      {latLng: [-8.51, 179.21], name: 'Tuvalu'},
      {latLng: [43.93, 12.46], name: 'San Marino'},
      {latLng: [47.14, 9.52], name: 'Liechtenstein'},
      {latLng: [7.11, 171.06], name: 'Marshall Islands'},
      {latLng: [17.3, -62.73], name: 'Saint Kitts and Nevis'},
      {latLng: [3.2, 73.22], name: 'Maldives'},
      {latLng: [35.88, 14.5], name: 'Malta'},
      {latLng: [12.05, -61.75], name: 'Grenada'},
      {latLng: [13.16, -61.23], name: 'Saint Vincent and the Grenadines'},
      {latLng: [13.16, -59.55], name: 'Barbados'},
      {latLng: [17.11, -61.85], name: 'Antigua and Barbuda'},
      {latLng: [-4.61, 55.45], name: 'Seychelles'},
      {latLng: [7.35, 134.46], name: 'Palau'},
      {latLng: [42.5, 1.51], name: 'Andorra'},
      {latLng: [14.01, -60.98], name: 'Saint Lucia'},
      {latLng: [6.91, 158.18], name: 'Federated States of Micronesia'},
      {latLng: [1.3, 103.8], name: 'Singapore'},
      {latLng: [1.46, 173.03], name: 'Kiribati'},
      {latLng: [-21.13, -175.2], name: 'Tonga'},
      {latLng: [15.3, -61.38], name: 'Dominica'},
      {latLng: [-20.2, 57.5], name: 'Mauritius'},
      {latLng: [26.02, 50.55], name: 'Bahrain'},
      {latLng: [0.33, 6.73], name: 'São Tomé and Príncipe'}
    ]
  });

  /* SPARKLINE CHARTS
   * ----------------
   * Create a inline charts with spark line
   */

  //-----------------
  //- SPARKLINE BAR -
  //-----------------
  $('.sparkbar').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'bar',
      height: $this.data('height') ? $this.data('height') : '30',
      barColor: $this.data('color')
    });
  });

  //-----------------
  //- SPARKLINE PIE -
  //-----------------
  $('.sparkpie').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'pie',
      height: $this.data('height') ? $this.data('height') : '90',
      sliceColors: $this.data('color')
    });
  });

  //------------------
  //- SPARKLINE LINE -
  //------------------
  $('.sparkline').each(function () {
    var $this = $(this);
    $this.sparkline('html', {
      type: 'line',
      height: $this.data('height') ? $this.data('height') : '90',
      width: '100%',
      lineColor: $this.data('linecolor'),
      fillColor: $this.data('fillcolor'),
      spotColor: $this.data('spotcolor')
    });
  });
});
