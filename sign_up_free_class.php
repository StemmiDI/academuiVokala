<link rel="stylesheet" href="css/sign_up_fclass.css">
<section class="signup-container">
  <?php
  include "app/db.php";
  $message = ''; // Initialize message variable

  // Check if form is submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the form
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];

    // Sanitize inputs to prevent SQL injection and other security issues
    $fullName = htmlspecialchars(trim($fullName));
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $message = "Invalid email format";
    } else {
      // Prepare SQL query to insert data into the database
      $sql = "INSERT INTO free_lesson (free_lesson_name, free_lesson_email) VALUES (:fullName, :email)";
      $stmt = $pdo->prepare($sql);

      // Bind the parameters to the SQL query
      $stmt->bindParam(':fullName', $fullName);
      $stmt->bindParam(':email', $email);

      // Execute the query
      if ($stmt->execute()) {
        $message = "Success! You have signed up for the free lesson. Please wait for our response.";
      } else {
        $message = "Something went wrong. Please try again.";
      }
    }
  }
  ?>


  <div class="signup-wrapper">
    <img
      src="img/free_class.png"
      alt=""
      class="background-image"
      loading="lazy" />
    <div class="form-container">
      <h1 class="signup-title">Запишитесь на первое занятие бесплатно</h1>
      <p class="signup-description">
        Приходите на занятие, чтобы узнать положение вашего голоса, научиться упражнениям дыхания и дать волю вашим
        эмоциям посредством вокала (занятие длится 1 час).
      </p>
      <form class="signup-form" method="POST" action="sign_up_free_class.php">
        <label for="fullName" class="input-label">Имя</label>
        <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Иван Иванов" required />

        <label for="email" class="email-label">Почта</label>
        <input type="email" id="email" name="email" class="form-input" placeholder="business@mail.com" required />

        <div class="privacy-container">
          <input type="checkbox" id="privacy" name="privacy" class="visually-hidden" required />
          <label for="privacy">Я соглашаюсь на обработку персональных данных</label>
        </div>

        <button type="submit" class="submit-button">Записаться на пробное занятие</button>
      </form>
      <script>
        // Function to show popup
        function showPopup(message) {
          const popup = document.createElement('div');
          popup.classList.add('popup');
          popup.innerHTML = `
      <div class="popup-message">
        <p>${message}</p>
        <button class="popup-close">Close</button>
      </div>
    `;
          document.body.appendChild(popup);

          // Close the popup when the close button is clicked
          const closeBtn = popup.querySelector('.popup-close');
          closeBtn.addEventListener('click', () => {
            popup.remove();
          });
        }

        // Check if there is a message from PHP and show the popup
        <?php if ($message): ?>
          showPopup("<?php echo $message; ?>");
        <?php endif; ?>
      </script>
      <style>
        /* Popup styles */
        .popup {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
          display: flex;
          justify-content: center;
          align-items: center;
          z-index: 9999;
        }

        .popup-message {
          background-color: white;
          padding: 20px;
          border-radius: 8px;
          text-align: center;
          width: 80%;
          max-width: 400px;
        }

        .popup-message p {
          font-size: 16px;
          margin-bottom: 20px;
        }

        .popup-close {
          background-color: #007BFF;
          color: white;
          border: none;
          padding: 10px 20px;
          font-size: 14px;
          cursor: pointer;
          border-radius: 5px;
        }

        .popup-close:hover {
          background-color: #0056b3;
        }
      </style>
    </div>
  </div>
</section>