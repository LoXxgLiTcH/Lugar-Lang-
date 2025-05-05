let jeepneyData = {};

fetch('route.json')
  .then(response => {
    if (!response.ok) throw new Error('Network response was not ok');
    return response.json();
  })
  .then(data => {
    jeepneyData = data.routes.reduce((acc, route) => {
      acc[route.id] = route;
      return acc;
    }, {});

    document.querySelectorAll('.jeep-button').forEach(btn => {
      btn.disabled = false;
    });
  })
  .catch(error => {
    console.error('Error loading jeepney data:', error);
  });

  function showRoute(code) {
    const route = jeepneyData[code];
    if (route) {
      console.log(route);  
  
     const content = `
  <div class="popup-header">
    <h2 class="route-title">${route.name} Route</h2>
    <div class="route-info">
      <p><strong>From:</strong> <span class="route-detail">${route.from}</span></p>
      <p><strong>To:</strong> <span class="route-detail">${route.to}</span></p>
    </div>
  </div>
  <h3 class="waypoints-title">Waypoints:</h3>
  <ul class="timeline">
    ${route.waypoints.map(wp => `
      <li class="timeline-item">
        <div class="timeline-icon"></div>
        <h4 class="waypoint-name">${wp.name}</h4>
        ${wp.type ? `<span class="badge ${wp.type.toLowerCase().replace(/ /g, '-')}">${wp.type}</span>` : ''}
      </li>
    `).join('')}
  </ul>
  ${route.return_waypoints && route.return_waypoints.length > 0 ? `
    <h3 class="waypoints-title">Return Waypoints:</h3>
    <ul class="timeline">
      ${route.return_waypoints.map(wp => `
        <li class="timeline-item">
          <div class="timeline-icon"></div>
          <h4 class="waypoint-name">${wp.name}</h4>
          ${wp.type ? `<span class="badge ${wp.type.toLowerCase().replace(/ /g, '-')}">${wp.type}</span>` : ''}
        </li>
      `).join('')}
    </ul>
  ` : ''}
`;

  
      if (code === '13C') {
        document.getElementById('popup-content').innerHTML = content;
        document.getElementById('popup').classList.add('show');
      } else if (code === '13H') {
        document.getElementById('popup-content2').innerHTML = content;
        document.getElementById('popup2').classList.add('show');
      } else if (code === '13B') {
        document.getElementById('popup-content3').innerHTML = content;
        document.getElementById('popup3').classList.add('show');
      } else if (code === '62B') {
        document.getElementById('popup-content3').innerHTML = content;
        document.getElementById('popup3').classList.add('show');
      } else if (code === '62C') {
        document.getElementById('popup-content4').innerHTML = content;
        document.getElementById('popup4').classList.add('show');
      } else if (code === '12D') {
        document.getElementById('popup-content5').innerHTML = content;
        document.getElementById('popup5').classList.add('show');
      } else if (code === '01C') {
        document.getElementById('popup-content6').innerHTML = content;
        document.getElementById('popup6').classList.add('show');
      } else if (code === '01B') {
        document.getElementById('popup-content7').innerHTML = content;
        document.getElementById('popup7').classList.add('show');
      } else if (code === '10F') {
        document.getElementById('popup-content8').innerHTML = content;
        document.getElementById('popup8').classList.add('show');
      } else if (code === '10H') {
        document.getElementById('popup-content9').innerHTML = content;
        document.getElementById('popup9').classList.add('show');
      } else if (code === '17C') {
        document.getElementById('popup-content10').innerHTML = content;
        document.getElementById('popup10').classList.add('show');
      } else if (code === '12I') {
        document.getElementById('popup-content11').innerHTML = content;
        document.getElementById('popup11').classList.add('show');
      } else if (code === '09F') {
        document.getElementById('popup-content12').innerHTML = content;
        document.getElementById('popup12').classList.add('show');
      } else if (code === '02B') {
        document.getElementById('popup-content13').innerHTML = content;
        document.getElementById('popup13').classList.add('show');
      } else if (code === '04C') {
        document.getElementById('popup-content14').innerHTML = content;
        document.getElementById('popup14').classList.add('show');
      } else if (code === '04L') {
        document.getElementById('popup-content15').innerHTML = content;
        document.getElementById('popup15').classList.add('show');
      } else if (code === '14D') {
        document.getElementById('popup-content16').innerHTML = content;
        document.getElementById('popup16').classList.add('show');
      } else if (code === '17C') {
        document.getElementById('popup-content17').innerHTML = content;
        document.getElementById('popup17').classList.add('show');
      }
    } else {
      alert('Route not found!');
    }
  }
  
  
  function closePopup() {
    document.getElementById('popup').classList.remove('show'); 
  }
  
  function closePopup2() {
    document.getElementById('popup2').classList.remove('show'); 
  }
  
  function closePopup3() {
    document.getElementById('popup3').classList.remove('show');
  }

  function closePopup4() {
    document.getElementById('popup4').classList.remove('show');
  }
  function closePopup5() {
    document.getElementById('popup5').classList.remove('show');
  }

  function closePopup6() {
    document.getElementById('popup6').classList.remove('show');
  }
  
  function closePopup7() {
    document.getElementById('popup7').classList.remove('show');
  }

  function closePopup8() {
    document.getElementById('popup8').classList.remove('show');
  }

  
  function closePopup9() {
    document.getElementById('popup9').classList.remove('show');
  }

  function closePopup10() {
    document.getElementById('popup10').classList.remove('show');
  }

  function closePopup11() {
    document.getElementById('popup11').classList.remove('show');
  }

  function closePopup12() {
    document.getElementById('popup12').classList.remove('show');
  }

  function closePopup13() {
    document.getElementById('popup13').classList.remove('show');
  }

  function closePopup14() {
    document.getElementById('popup14').classList.remove('show');
  }

  function closePopup15() {
    document.getElementById('popup15').classList.remove('show');
  }
  function closePopup16() {
    document.getElementById('popup16').classList.remove('show');
  }
  function closePopup17() {
    document.getElementById('popup17').classList.remove('show');
  }