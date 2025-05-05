
<?php include '../nav/nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us | Lugar Lang</title>
  <link rel="stylesheet" href="about-page.css" />
  </style>
</head>
<body>
  <div class="header">
    <div class="flex align-center gap-1">
      <img class="header-icon" alt="Users Icon" src="../../public/icons/users-solid.svg" />
      <span class="header-title">About Us</span>
    </div>
  </div>

  <div class="hero-section">
    <div class="logo-container">
      <img class="logo" alt="Lugar Lang Logo" src="../../public/assets/lugar-lang-logo.png" />
    </div>
    <h1 class="hero-title">The Team Behind Lugar Lang!</h1>
  </div>

  <div class="container">
    <div class="intro-card">
      <h2 class="section-title">A Web Dev I Project by DevWebs</h2>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda atque, aspernatur natus consectetur esse reiciendis rem repellat quod, illo reprehenderit corrupti dignissimos impedit ipsam quaerat suscipit, distinctio exercitationem unde itaque?/p>
    </div>

    <div class="team-section">
      <h2 class="section-title">Meet The Team! </h2>
      
      <div class="team-grid">
        <div class="team-member">
          <img class="member-photo" alt="Homer's Photo" src="/api/placeholder/80/80" />
          <h3 class="member-name">Homer</h3>
          <p class="member-role">Project Manager & Back-End Developer</p>
          <p class="member-desc">Leading the team with project direction and member coordination. Homer ensures smooth development cycles and maintains the backbone of the Lugar Lang! application, both in front-end and back-end contexts.</p>
        </div>

        <div class="team-member">
          <img class="member-photo" alt="Jade's Photo" src="/api/placeholder/80/80" />
          <h3 class="member-name">Jade</h3>
          <p class="member-role">Full-Stack Developer</p>
          <p class="member-desc">Versatile in both front-end and back-end technologies, Jade is responsible for the implementation of the Google Maps API in Lugar Lang that allows for navigation functionality which displays the relevant routes and other data pulled from Google's Maps API.</p>
        </div>

        <div class="team-member">
          <img class="member-photo" alt="James's Photo" src="/api/placeholder/80/80" />
          <h3 class="member-name">James</h3>
          <p class="member-role">Database Engineer</p>
          <p class="member-desc">The architect behind the MySQL Database, James designed the backend architecture that allowed for the optimal storage and retrieval of information which includes the user database and their corresponding information.>
        </div>

        <div class="team-member">
          <img class="member-photo" alt="Leira's Photo" src="/api/placeholder/80/80" />
          <h3 class="member-name">Leira</h3>
          <p class="member-role">UI/UX Designer</p>
          <p class="member-desc">With a keen eye for aesthetics and user behavior, Leira designs intuitive interfaces that makes the user experience through our app a pleasant experience.</p>
        </div>

        <div class="team-member">
          <img class="member-photo" alt="Eunice's Photo" src="/api/placeholder/80/80" />
          <h3 class="member-name">Eunice</h3>
          <p class="member-role">UI/UX Designer</p>
          <p class="member-desc">Combining creativity with user-centric design principles, Eunice works alongside Leira to craft visually appealing and functional elements throughout the app.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    <p>Lugar Lang niya, boss!</p>
    <p class="copyright">© 2025 DevWebs. All rights reserved.</p>
  </div>
</body>
</html>