layui.use(['element', 'form', 'layer'], function(){
  var element = layui.element;
  var form = layui.form;
  var layer = layui.layer;
  var master_data;

var viewer = new Cesium.Viewer('cesiumContainer',{
  timeline : false,
  animation : false,
  fullscreenButton : false,
  skyBox : new Cesium.SkyBox({  
    sources : {  
      positiveX : '../src/starspace_1.jpg',  
      negativeX : '../src/starspace_1.jpg',  
      positiveY : '../src/starspace_1.jpg',  
      negativeY : '../src/starspace_1.jpg',  
      positiveZ : '../src/starspace_1.jpg',  
      negativeZ : '../src/starspace_1.jpg'  
    }
  }),
  baseLayerPicker : false,
  imageryProvider : new Cesium.UrlTemplateImageryProvider({
    url: "https://webst02.is.autonavi.com/appmaptile?style=6&x={x}&y={y}&z={z}",
    layer: "tdtVecBasicLayer",
    style: "default",
    format: "image/png",
    tileMatrixSetID: "GoogleMapsCompatible",
  }),
});
viewer.cesiumWidget.creditContainer.style.display = "none";

function flyto(x, y, z, t){
  viewer.camera.flyTo({
    destination: Cesium.Cartesian3.fromDegrees(x, y, z),
    orientation : {
        heading : Cesium.Math.toRadians(0),
        pitch : Cesium.Math.toRadians(-90),
        roll : 0
    },
    duration: t,
  });
  setTimeout(function() {
    adaptScale(viewer.camera.positionCartographic.height);
  }, 1500);
  setTimeout(function() {
    adaptScale(viewer.camera.positionCartographic.height);
  }, 3000);
}
setTimeout(function() {
  flyto(119, 0, 13000000, 3);
}, 2000);

viewer.screenSpaceEventHandler.setInputAction(function(click) {
  var feature = viewer.scene.pick(click.position);
  if(feature === undefined) return;
  for(let i=0; i<master_data.length; i++){
    var entity = 'entity_'+i;
    if(feature.id._name === window[entity].name){
      var namestr = "'"+master_data[i].name+"'";
      $.get('work-info.php?name='+namestr, function(response){
        $("#masterBody").empty();
        if(response===""){
          $("#masterTable").addClass('no-see');
          return;
        }
        var trim_line_break=response.replace(/\r\n/g, ""); //Json cannot parse a newline character
        var work_data=JSON.parse(trim_line_break);

        $("#masterTable").removeClass("no-see");
        for(let i=0; i<Math.min(2,work_data.length); i++){ //It can hold up to two rows of data, and the detail page shows all the data
          $("#masterBody").append("<tr><td>"+work_data[i].w_name+"</td>"+"<td>"+work_data[i].w_time+"</td>"+"<td>"+work_data[i].w_type+"</td></tr>");
        }
      });
      $.get('famous-php.php?name='+namestr, function(response){
        var trim_line_break=response.replace(/\r\n/g, "");
        trim_line_break=trim_line_break.replace(/<br>/g, "");
        var people_data=JSON.parse(trim_line_break);
        var name=people_data.name;
        var picpath=people_data.pict;
        var detinfo=people_data.det_info.substring(0,70)+"...";

        $('#moreInfo').off('click');
        $("#infopane").removeClass('no-see');
        $("#name").text(name);
        $("#portrait").attr("src",picpath);
        $("#baseinfo").text(detinfo);
        
        $('#closeBtn').on('click',function(){
          $("#infopane").addClass('no-see');
        });
        $('#moreInfo').on('click',function(){
          layer.open({
            type: 2,
            title: "It's the details you want~",
            content: 'marster-info.php?name='+namestr,
            area: ['1349px', '656px'],
            maxmin: true,
            anim: 4
          });
        });
      });
      $('#trace').off('click');
      $('#trace').on('click',function(){
          $.ajax({
            url: "route/" + master_data[i].name + ".html",
            success : function() {
              layer.open({
                type: 2,
                title: '是你想要的人物轨迹yo~',
                content: "route/" + master_data[i].name + ".html",
                area: ['1349px', '656px'],
                maxmin: true,
                anim: 4
              });
            },
            error : function(e){
              layer.msg('他/她还没有轨迹路线噢', {
                icon: 5,
                time: 2000,
                btn: ['朕知道了'],
                area: ['80px', '140px'],
                btnAlign: 'c',
                anim: 4
              });
            }
          })
      });
      break;
    }
  }
}, Cesium.ScreenSpaceEventType.LEFT_CLICK);

$.ajaxSettings.async = false;
$.get('master-number.php', function(response){
  master_data=JSON.parse(response);
  for(let i=0; i < master_data.length; i++){
    var entity = 'entity_'+i;
    window[entity] = viewer.entities.add({
      name: master_data[i].name,
      position: Cesium.Cartesian3.fromDegrees(master_data[i].lon, master_data[i].lat),
      point: {
          color: Cesium.Color.POWDERBLUE,
          pixelSize: 10,
          show: false
      },
      label : {
          text : master_data[i].name,
          font : '14pt cursive',
          fillColor:Cesium.Color.PAPAYAWHIP,
          pixelOffset:new Cesium.Cartesian2(0,0),
          show: true
      },
      billboard: {
        image: '../src/bracket3.png',
        pixelOffset:new Cesium.Cartesian2(0,0),
        show: true
      }
    });
  }
});
// $.ajaxSettings.async = true;

function adaptScale(height){
  if(height<2000000){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.scale = 85000/height;
      window[entity].label.scale = 900000/height;
      window[entity].point.pixelSize = 9000000/height;
    }
  }
  else {
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.scale = 350000/height;
      window[entity].label.scale = 4000000/height;
      window[entity].point.pixelSize = 50000000/height;
    }
  }
}
adaptScale(13000000);

viewer.screenSpaceEventHandler.setInputAction(function(wheelment) { //贴图大小随图缩放，并且限定地图大小范围
  var height=viewer.camera.positionCartographic.height;
  adaptScale(height);

  if(height<600000){
    viewer.camera.setView({
      destination : Cesium.Cartesian3.fromRadians(viewer.camera.positionCartographic.longitude, viewer.camera.positionCartographic.latitude, 600000)
    });
  };
  if(height>40000000){
    viewer.camera.setView({
      destination : Cesium.Cartesian3.fromRadians(viewer.camera.positionCartographic.longitude, viewer.camera.positionCartographic.latitude, 40000000)
    });
  }
}, Cesium.ScreenSpaceEventType.WHEEL);

//Realizing the rotation of the earth
var i = Date.now();
function rotate() {
  var a = .1;
  var t = Date.now();
  var n = (t - i) / 1e3;
  i = t;
  viewer.scene.camera.rotate(Cesium.Cartesian3.UNIT_Z, -a * n);
}
viewer.clock.onTick.addEventListener(rotate);

form.on('submit(formDemo)', function(findinfo){
  for(let i=0; i < master_data.length; i++){
    var entity = 'entity_'+i;
    window[entity].billboard.image = '../src/bracket3.png';
    window[entity].point.color = Cesium.Color.POWDERBLUE;
  }
  var findinfo = "'%"+findinfo.field.findit+"%'";
  $.get('find-it.php?findinfo='+findinfo, function(response){
    if(response === ""){
      layer.msg('404 not found', {
        icon: 5,
        time: 2000,
        btn: ['朕知道了'],
        area: ['80px', '120px'],
        btnAlign: 'c',
        anim: 4
      });
    }
    else{
      var namelist=JSON.parse(response);
      if(namelist.length == 1){
        $('#leftCloud').toggleClass('left-cloud-out');
        $('#leftCloud').toggleClass('left-cloud-in');
        $('#rightCloud').toggleClass('right-cloud-out');
        $('#rightCloud').toggleClass('right-cloud-in');
        viewer.clock.onTick.removeEventListener(rotate);
        $("input[name=earthRotate]+div").removeClass("layui-form-onswitch");
        $("input[name=earthRotate]")[0].checked=false;
        flyto(namelist[0].lon, namelist[0].lat, 1000000, 4);
        for(let i=0; i < master_data.length; i++){
          var entity = 'entity_'+i;
          if(window[entity].name == namelist[0].name){
            window[entity].billboard.image = '../src/bracket1.png';
            window[entity].point.color = Cesium.Color.LIGHTPINK;
          }
        }
      }
      else {
        $('#leftCloud').toggleClass('left-cloud-out');
        $('#leftCloud').toggleClass('left-cloud-in');
        $('#rightCloud').toggleClass('right-cloud-out');
        $('#rightCloud').toggleClass('right-cloud-in');
        viewer.clock.onTick.removeEventListener(rotate);
        $("input[name=earthRotate]+div").removeClass("layui-form-onswitch");
        $("input[name=earthRotate]")[0].checked=false;
        flyto(namelist[0].lon, namelist[0].lat, 1000000, 4);
        for(let i=0; i < master_data.length; i++){
          var entity = 'entity_'+i;
          for(let j=0; j < namelist.length; j++){
            if(window[entity].name == namelist[j].name){
              window[entity].billboard.image = '../src/bracket1.png';
              window[entity].point.color = Cesium.Color.LIGHTPINK;
              namelist.splice(j,1); 
            }
          }
        }
      }
    }
  });
  return false;
});


form.on('switch(peopleName)', function(){
  if(this.checked === true){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].label.show=true;
    }
  }
  else{
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].label.show=false;
    }
  }
});
form.on('switch(peopleBracket)', function(){
  if(this.checked === true){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.show=true;
    }
  }
  else{
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.show=false;
    }
  }
});
form.on('switch(peoplePoint)', function(){
  if(this.checked === true){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].point.show=true;
    }
  }
  else{
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].point.show=false;
    }
  }
});
form.on('switch(earthRotate)', function(){
  if(this.checked === true){
    viewer.clock.onTick.addEventListener(rotate);
  }
  else{
    viewer.clock.onTick.removeEventListener(rotate);
  }
});
form.on('switch(cloudRemove)', function(){
  if(this.checked === true){
    $("#leftCloud").removeClass("no-see");
    $("#rightCloud").removeClass("no-see");
  }
  else{
    $("#leftCloud").addClass("no-see");
    $("#rightCloud").addClass("no-see");
  }
});

function button_true(findinfo, pict_path, flag){
  for(let i=1; i < age_input.length; i++){
    if($("input[name="+flag+"]")[i].checked === false){ break; }
    if($("input[name="+flag+"]")[i].checked === true && i == age_input.length-1){
      $("#"+flag+"_0>div").addClass("layui-form-checked");
      $("input[name="+flag+"]")[0].checked=true;
    }
  }
  var data_1;
  $.get('find-it.php?findinfo='+findinfo, function(response){
    if(response === ""){
      data_1 = [];
    }
    else {
      data_1 = JSON.parse(response);
    }
  })
  if(data_1.length == 0){
    layer.msg('这里并没有人噢', {
      icon: 5,
      time: 2000,
      btn: ['朕知道了'],
      area: ['80px', '140px'],
      btnAlign: 'c',
      anim: 4
    });
  }
  else {
    $('#leftCloud').toggleClass('left-cloud-out');
    $('#leftCloud').toggleClass('left-cloud-in');
    $('#rightCloud').toggleClass('right-cloud-out');
    $('#rightCloud').toggleClass('right-cloud-in');
    viewer.clock.onTick.removeEventListener(rotate);
    $("input[name=earthRotate]+div").removeClass("layui-form-onswitch");
    $("input[name=earthRotate]")[0].checked=false;
    flyto(data_1[0].lon, data_1[0].lat, 5000000, 4);
  }
  for(let i=0; i < master_data.length; i++){
    var entity = 'entity_'+i;
    for(let j=0; j < data_1.length; j++){
      if(window[entity].name == data_1[j].name){
        window[entity].billboard.image = pict_path;
        window[entity].point.color = Cesium.Color.LIGHTPINK;
        if(document.getElementsByName("peoplePoint")[0].checked){
          window[entity].point.show=true;
        }
        data_1.splice(j,1); 
      }
    }
  }
}

function button_false(findinfo, pict_path, flag){
  $("#"+flag+"_0>div").removeClass("layui-form-checked");
  $("input[name="+flag+"]")[0].checked=false;
  var data_1;
  $.get('find-it.php?findinfo='+findinfo, function(response){
    if(response === ""){
      data_1 = [];
    }
    else {
      data_1 = JSON.parse(response);
    }
  })
  for(let i=0; i < master_data.length; i++){
    var entity = 'entity_'+i;
    for(let j=0; j < data_1.length; j++){
      if(window[entity].name == data_1[j].name){
        window[entity].billboard.image = pict_path;
        window[entity].point.color = Cesium.Color.POWDERBLUE;
        data_1.splice(j,1); 
      }
    }
  }
}

//Age
var age_input = document.getElementsByName("age");
form.on('checkbox(age_0)', function(){
  if(this.checked === true){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.image = '../src/bracket1.png';
      window[entity].point.color = Cesium.Color.LIGHTPINK;
      if(document.getElementsByName("peoplePoint")[0].checked){
        window[entity].point.show=true;
      }
    }
    $('#leftCloud').toggleClass('left-cloud-out');
    $('#leftCloud').toggleClass('left-cloud-in');
    $('#rightCloud').toggleClass('right-cloud-out');
    $('#rightCloud').toggleClass('right-cloud-in');
    flyto(master_data[0].lon, master_data[0].lat, 5000000, 4);
    for(let i=0; i < age_input.length; i++){
      var age = '#age_'+i+'>div';
      $(age).addClass("layui-form-checked");
      $("input[name=age]")[i].checked=true;
    }
  }
  else{
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.image = '../src/bracket3.png';
      window[entity].point.color = Cesium.Color.POWDERBLUE;
      if(!document.getElementsByName("peoplePoint")[0].checked){
        window[entity].point.show=false;
      }
    }
    for(let i=0; i < age_input.length; i++){
      var age = '#age_'+i+'>div';
      $(age).removeClass("layui-form-checked");
      $("input[name=age]")[i].checked=false;
    }
  }
});

var age_list = ['古典时期', '中世纪', '文艺复兴时期', '工业时代', '现代', '原子能时代', '信息时代'];
for(let i=1; i<=age_list.length; i++){
  form.on('checkbox(age_'+i+')', function(){
    var flag = "age";
    if(this.checked === true){
      var findinfo = "'%"+age_list[i-1]+"%'";
      var pict_path = '../src/bracket1.png';
      button_true(findinfo, pict_path, flag);
    }
    else{
      var findinfo = "'%"+age_list[i-1]+"%'";
      var pict_path = '../src/bracket3.png';
      button_false(findinfo, pict_path, flag);
    }
  });
}

//Type
var type_input = document.getElementsByName("type");
form.on('checkbox(type_0)', function(){
  if(this.checked === true){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.image = '../src/bracket1.png';
      window[entity].point.color = Cesium.Color.LIGHTPINK;
      if(document.getElementsByName("peoplePoint")[0].checked){
        window[entity].point.show=true;
      }
    }
    $('#leftCloud').toggleClass('left-cloud-out');
    $('#leftCloud').toggleClass('left-cloud-in');
    $('#rightCloud').toggleClass('right-cloud-out');
    $('#rightCloud').toggleClass('right-cloud-in');
    flyto(master_data[0].lon, master_data[0].lat, 5000000, 4);
    for(let i=0; i < type_input.length; i++){
      var type = '#type_'+i+'>div';
      $(type).addClass("layui-form-checked");
      $("input[name=type]")[i].checked=true;
    }
  }
  else{
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.image = '../src/bracket3.png';
      window[entity].point.color = Cesium.Color.POWDERBLUE;
      if(!document.getElementsByName("peoplePoint")[0].checked){
        window[entity].point.show=false;
      }
    }
    for(let i=0; i < type_input.length; i++){
      var type = '#type_'+i+'>div';
      $(type).removeClass("layui-form-checked");
      $("input[name=type]")[i].checked=false;
    }
  }
});

var type_list = ['文学家', '军事家', '工程师', '商业家', '科学家', '宗教学', '艺术家', '音乐家'];
for(let i=1; i<=type_list.length; i++){
  form.on('checkbox(type_'+i+')', function(){
    var flag = "type";
    if(this.checked === true){
      var findinfo = "'%"+type_list[i-1]+"%'";
      var pict_path = '../src/bracket1.png';
      button_true(findinfo, pict_path, flag);
    }
    else{
      var findinfo = "'%"+type_list[i-1]+"%'";
      var pict_path = '../src/bracket3.png';
      button_false(findinfo, pict_path, flag);
    }
  });
}

//Country
var country_input = document.getElementsByName("country");
form.on('checkbox(country_0)', function(){
  if(this.checked === true){
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.image = '../src/bracket1.png';
      window[entity].point.color = Cesium.Color.LIGHTPINK;
      if(document.getElementsByName("peoplePoint")[0].checked){
        window[entity].point.show=true;
      }
    }
    $('#leftCloud').toggleClass('left-cloud-out');
    $('#leftCloud').toggleClass('left-cloud-in');
    $('#rightCloud').toggleClass('right-cloud-out');
    $('#rightCloud').toggleClass('right-cloud-in');
    flyto(master_data[0].lon, master_data[0].lat, 5000000, 4);
    for(let i=0; i < country_input.length; i++){
      var country = '#country_'+i+'>div';
      $(country).addClass("layui-form-checked");
      $("input[name=country]")[i].checked=true;
    }
  }
  else{
    for(let i=0; i < master_data.length; i++){
      var entity = 'entity_'+i;
      window[entity].billboard.image = '../src/bracket3.png';
      window[entity].point.color = Cesium.Color.POWDERBLUE;
      if(!document.getElementsByName("peoplePoint")[0].checked){
        window[entity].point.show=false;
      }
    }
    for(let i=0; i < country_input.length; i++){
      var country = '#country_'+i+'>div';
      $(country).removeClass("layui-form-checked");
      $("input[name=country]")[i].checked=false;
    }
  }
});

var country_list = ['中国','美国','英国','日本','德国','前苏联','法国','朝鲜','西班牙','阿富汗','印度尼西亚','蒙古','印度','斯里兰卡','委内瑞拉','安哥拉','瑞典','几内亚','澳大利亚','奥地利','俄国','捷克','匈牙利','埃及','巴基斯坦'];
for(let i=1; i<=country_list.length; i++){
  form.on('checkbox(country_'+i+')', function(){
    var flag = "country";
    if(this.checked === true){
      var findinfo = "'%"+country_list[i-1]+"%'";
      var pict_path = '../src/bracket1.png';
      button_true(findinfo, pict_path, flag);
    }
    else{
      var findinfo = "'%"+country_list[i-1]+"%'";
      var pict_path = '../src/bracket3.png';
      button_false(findinfo, pict_path, flag);
    }
  });
}
});