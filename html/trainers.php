<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trainers - Pranayom Yoga Center</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
      /* Specific Styles for Trainers Page */
      .trainers-hero {
        text-align: left;
        padding: 50px 10%;
      }

      .trainers-hero h1 {
        font-size: 36px;
        color: #222;
        margin-bottom: 20px;
      }

      .trainers-hero p {
        color: #555;
        max-width: 800px;
        line-height: 1.6;
        margin-bottom: 40px;
      }

      .trainer-grid {
        display: grid;
        grid-template-columns: repeat(
          4,
          1fr
        ); /* 4 columns as per image roughly, or maybe 4 cards in a row? Design seems to have 4 in top row */
        gap: 20px;
        padding: 0 10% 50px 10%;
      }

      .trainer-card {
        background-color: transparent;
        /* No border or shadow as per design simplicity, just image and text */
        display: flex;
        flex-direction: column;
        align-items: flex-start;
      }

      .trainer-card-img {
        background-color: #ffe0b2; /* Light orange bg for image as seen in one card */
        border-radius: 10px;
        width: 100%;
        height: 200px; /* Aspect ratio */
        display: flex;
        justify-content: center;
        align-items: flex-end;
        margin-bottom: 15px;
        overflow: hidden;
      }

      /* Varying backgrounds for cards if needed, can use nth-child */
      .trainer-card:nth-child(2) .trainer-card-img {
        background-color: #ffccbc;
      }
      .trainer-card:nth-child(3) .trainer-card-img {
        background-color: #fff9c4;
      }
      .trainer-card:nth-child(4) .trainer-card-img {
        background-color: #ffe0b2;
      } /* Repeating */

      .trainer-card-img img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
      }

      .trainer-info h3 {
        font-size: 18px;
        color: #222;
        margin-bottom: 5px;
      }

      .trainer-info p {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
      }

      /* Responsive grid if window is small, though instructions said "can't make a website responsive" 
           However, grid-template-columns: repeat(4, 1fr) is rigid but fits the instruction "no extra responsive".
        */
    </style>
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
          <li>
            <a href="trainers.php" style="font-weight: bold; color: #000"
              >Trainers</a
            >
          </li>
          <li><a href="contact.php">Contact</a></li>
        </div>
        <button class="btn-login" onclick="window.location.href='login.php'">
          Login
        </button>
      </div>

      <!-- Content Section -->
      <div class="secBox" style="background-color: #f8fcf8">
        <!-- Light finish -->
        <div class="trainers-hero">
          <h1>Our Expert Yoga Trainers</h1>
          <p>
            Meet our team of certified and experienced yoga instructors
            dedicated to guiding you on your wellness journey. Each trainer
            brings a unique style and expertise to help you achieve your
            personal goals.
          </p>
        </div>

        <div class="trainer-grid">
          <!-- Trainer 1 -->
          <div class="trainer-card">
            <div class="trainer-card-img">
              <!-- Using available images as placeholders -->
              <img
                src="../images/full-shot-man-doing-warrior-pose-indoor.jpg"
                alt="Billal Sheikh"
              />
            </div>
            <div class="trainer-info">
              <h3>Billal Sheikh</h3>
              <p>
                Elena's classes focus on mindful movement and breathwork,
                creating a serene and restorative experience for all levels.
              </p>
            </div>
          </div>

          <!-- Trainer 2 -->
          <div class="trainer-card">
            <div class="trainer-card-img">
              <img
                src="../images/spiritual-young-man-practicing-yoga-indoors.jpg"
                alt="Diab Hossain"
              />
            </div>
            <div class="trainer-info">
              <h3>Diab Hossain</h3>
              <p>
                David specializes in dynamic vinyasa flow, blending strength and
                flexibility for a challenging yet rewarding practice.
              </p>
            </div>
          </div>

          <!-- Trainer 3 -->
          <div class="trainer-card">
            <div class="trainer-card-img" style="background-color: #fff3e0">
              <img
                src="../images/pregnant-woman-holding-fitness-mat.jpg"
                alt="Priya Sharma"
              />
            </div>
            <div class="trainer-info">
              <h3>Priya Sharma</h3>
              <p>
                Priya's expertise lies in traditional Hatha yoga, emphasizing
                alignment and balance for a holistic approach to wellness.
              </p>
            </div>
          </div>

          <!-- Trainer 4 -->
          <div class="trainer-card">
            <div class="trainer-card-img" style="background-color: #ffe0b2">
              <img
                src="../images/chelsea-gates-n8L1VYaypcw-unsplash.jpg"
                alt="Mamun"
              />
            </div>
            <div class="trainer-info">
              <h3>Mamun</h3>
              <p>
                Marcus brings a unique blend of yoga and fitness, offering
                high-energy classes that build strength and endurance.
              </p>
            </div>
          </div>

          <!-- Trainer 5 (Next Row) -->
          <div class="trainer-card">
            <div class="trainer-card-img" style="background-color: #fff9c4">
              <img
                src="../images/dane-wetton-t1NEMSm1rgI-unsplash.jpg"
                alt="Rose Islam"
              />
            </div>
            <div class="trainer-info">
              <h3>Rose Islam</h3>
              <p>
                Isabella's gentle and restorative yoga sessions are perfect for
                relaxation and stress relief.
              </p>
            </div>
          </div>

          <!-- Trainer 6 -->
          <div class="trainer-card">
            <div class="trainer-card-img" style="background-color: #ffe0b2">
              <img
                src="../images/dylan-gillis-YJdCZba0TYE-unsplash.jpg"
                alt="Ehan Pathan"
              />
            </div>
            <div class="trainer-info">
              <h3>Ehan Pathan</h3>
              <p>
                Ethan's classes incorporate elements of yoga therapy, focusing
                on injury prevention and rehabilitation.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer>
        <div class="footer-links">
          <span
            ><a href="policy.php" style="color: white; text-decoration: none"
              >Privacy Policy</a
            ></span
          >
          <span
            ><a href="terms.php" style="color: white; text-decoration: none"
              >Terms of Service</a
            ></span
          >
          <span
            ><a href="contact.php" style="color: white; text-decoration: none"
              >Contact Us</a
            ></span
          >
        </div>
        <p>@2024 Pranayom. All rights reserved.</p>
      </footer>
    </div>
  </body>
</html>
