<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pranayom Yoga Center</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <div class="mainContent">
    <!-- Navbar -->
    <div class="navbar">
      <div class="logo">
        <h3><a href="index.php">Pranayom</a></h3>
      </div>
      <div class="list">
        <li><a href="index.php">Home</a></li>
        <li><a href="membership.php">Membership Plans</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <li><a href="contact.php">Contact</a></li>
      </div>
      <a href="login.php" class="btn-login">Login</a>
    </div>

    <!-- Content Section -->
    <div class="secBox">
      <!-- Hero -->
      <div class="hero">
        <img src="../images/interior-design-yoga-space.jpg" alt="Yoga Studio" />
        <div class="hero-content">
          <h1>Find Your Inner Peace</h1>
          <p>
            Discover the transformative power of yoga with our expert
            instructors and serene studio environment.
          </p>
          <button class="btn-cta" onclick="window.location.href = 'classes.php'">
            Explore Classes
          </button>
        </div>
      </div>

      <!-- About -->
      <div style="padding: 50px; text-align: center">
        <h1>About Us</h1>
        <p style="padding-top: 20px">
          Pranayom is dedicated to providing a sanctuary for personal growth
          and well-being.Pranayom focuses on mindful breathing techniques
          designed to regulate energy, relax the nervous system, and promote
          mental clarity. It is suitable for all ages and helps create harmony
          between body and mind..
        </p>
      </div>

      <!-- Offerings -->
      <div style="background-color: #f9f9f9">
        <h1 style="text-align: center; padding-top: 50px">Our Offerings</h1>
        <div class="cardBox">
          <div class="card">
            <img src="../images/chelsea-gates-n8L1VYaypcw-unsplash.jpg" alt="Yoga Classes" />
            <div class="card-content">
              <h3>Yoga Classes</h3>
              <p>Explore a variety of yoga styles.</p>
            </div>
          </div>
          <div class="card">
            <img src="../images/dane-wetton-t1NEMSm1rgI-unsplash.jpg" alt="Meditation" />
            <div class="card-content">
              <h3>Meditation Sessions</h3>
              <p>Find inner peace with guided meditation.</p>
            </div>
          </div>
          <div class="card">
            <img src="../images/dylan-gillis-YJdCZba0TYE-unsplash.jpg" alt="Personal Training" />
            <div class="card-content">
              <h3>Personalized Training</h3>
              <p>Receive personalized guidance.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonials -->
      <div class="testimonials">
        <h2>Testimonials</h2>
        <div class="review">
          <div class="review-header">
            <div class="design">TM</div>
            <div>
              <strong>Tanim Reza</strong><br />
              <small>2 months ago</small>
            </div>
          </div>
          <div class="stars">★★★★★</div>
          <p>"Pranayom has transformed my life!"</p>
        </div>
        <div class="review">
          <div class="review-header">
            <div class="design" style="background-color: #90caf9">EF</div>
            <div>
              <strong>Ehan Fishman</strong><br />
              <small>3 months ago</small>
            </div>
          </div>
          <div class="stars">★★★★★</div>
          <p>"I was new to yoga but felt comfortable right away."</p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <div class="footer-links">
        <span><a href="policy.php" style="color: white; text-decoration: none">Privacy Policy</a></span>
        <span><a href="terms.php" style="color: white; text-decoration: none">Terms of Service</a></span>
        <span><a href="contact.php" style="color: white; text-decoration: none">Contact Us</a></span>
      </div>
      <p>@2024 Pranayom. All rights reserved.</p>
    </footer>
  </div>
</body>

</html>