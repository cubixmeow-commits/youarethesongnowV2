window.RuckMaps=(()=>{
    const api=document.body.dataset.api;
    let settings={enabled:false},current=null,positionMarker=null,pendingPosition=null;
    const ready=fetch(api+'?action=bootstrap',{credentials:'same-origin'}).then(r=>r.json()).then(s=>settings=s).catch(()=>settings);

    async function call(action,payload){
        await ready;
        const r=await fetch(api+'?action='+action,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-Token':settings.csrf},body:JSON.stringify(payload)});
        const body=await r.json();
        if(!r.ok||!body.ok)throw Error(body.error||'Map search is unavailable.');
        return body;
    }

    function remove(){
        if(positionMarker){positionMarker.remove();positionMarker=null;}
        if(current){current.remove();current=null;}
        pendingPosition=null;
    }

    function syncPosition(){
        if(!current||!current.loaded()||!pendingPosition)return;
        const {point,track}=pendingPosition;
        const coordinates=(track||[]).filter(p=>Number.isFinite(p?.longitude)&&Number.isFinite(p?.latitude)).map(p=>[p.longitude,p.latitude]);
        const progress=current.getSource('progress');
        if(progress)progress.setData({type:'Feature',properties:{},geometry:{type:'LineString',coordinates}});
        if(!point||!Number.isFinite(point.longitude)||!Number.isFinite(point.latitude))return;
        const location=[point.longitude,point.latitude];
        if(!positionMarker){
            const el=document.createElement('div');
            el.className='location-puck';
            el.setAttribute('role','img');
            el.setAttribute('aria-label','Your current location');
            positionMarker=new maplibregl.Marker({element:el}).setLngLat(location).addTo(current);
        }else positionMarker.setLngLat(location);
    }

    function updatePosition(point,track=[]){
        pendingPosition={point,track};
        syncPosition();
    }

    function draw(id,route){
        remove();
        if(!settings.enabled||!window.maplibregl||!document.getElementById(id))return;
        const geometry=route.geometry;
        const lines=geometry.type==='LineString'?[geometry.coordinates]:geometry.coordinates;
        const points=lines.flat();
        if(!points.length)return;
        const bounds=new maplibregl.LngLatBounds(points[0],points[0]);
        points.forEach(p=>bounds.extend(p));
        current=new maplibregl.Map({container:id,style:{version:8,sources:{base:{type:'raster',tiles:[new URL(api,location.href).href+'?action=tile&z={z}&x={x}&y={y}'],tileSize:256,attribution:'© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors · Powered by <a href="https://www.geoapify.com/">Geoapify</a>'}},layers:[{id:'base',type:'raster',source:'base'}]},bounds,fitBoundsOptions:{padding:35},maxZoom:18,attributionControl:{compact:false}});
        const map=current;
        map.addControl(new maplibregl.NavigationControl({showCompass:false}),'top-right');
        map.on('load',()=>{
            if(current!==map)return;
            map.addSource('walk',{type:'geojson',data:{type:'Feature',properties:{},geometry}});
            map.addLayer({id:'walk-outline',type:'line',source:'walk',paint:{'line-color':'#fffdf8','line-width':7}});
            map.addLayer({id:'walk',type:'line',source:'walk',paint:{'line-color':'#d54a0c','line-width':4}});
            map.addSource('progress',{type:'geojson',data:{type:'Feature',properties:{},geometry:{type:'LineString',coordinates:[]}}});
            map.addLayer({id:'progress-outline',type:'line',source:'progress',paint:{'line-color':'#fffdf8','line-width':7}});
            map.addLayer({id:'progress',type:'line',source:'progress',paint:{'line-color':'#327a90','line-width':4}});
            syncPosition();
        });
    }

    return {ready,enabled:()=>settings.enabled,search:text=>call('search',{text}),routes:args=>call('routes',args),draw,remove,updatePosition};
})();
