<!DOCTYPE html>
<html lang="uk">
<!-- сторінка дякую -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successful dispatch</title>
    <style>
        /* Загальні стилі */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Контейнер */
        .success-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Іконка */
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }

        /* Заголовок */
        .success-message {
            font-size: 24px;
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Опис */
        .success-description {
            font-size: 18px;
            color: #666;
            line-height: 1.5;
        }

        /* Анімація */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Адаптивність */
        @media (max-width: 768px) {
            .success-container {
                padding: 30px;
                max-width: 90%;
            }

            .success-icon {
                font-size: 50px;
            }

            .success-message {
                font-size: 20px;
            }

            .success-description {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 20px;
            }

            .success-icon {
                font-size: 40px;
            }

            .success-message {
                font-size: 18px;
            }

            .success-description {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1 class="success-message">Thank you for your request!</h1>
        <p class="success-description">We will call you back later.</p>
    </div>
</body>

</html>