<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Campus | Lugar Lang</title>
    <!-- Bootstrap CSS (Minimal) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --accent-orange: #ff6b35;
            --accent-green: #4caf50;
            --light-green: rgba(76, 175, 80, 0.1);
            --light-orange: rgba(255, 107, 53, 0.1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #121212 0%, var(--primary-blue) 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            position: relative;
            overflow: hidden;
        }

        /* Space-like elements */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(white 1px, transparent 0);
            background-size: 50px 50px;
            background-position: -25px -25px;
            opacity: 0.1;
            z-index: -1;
        }

        .page-container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 20px;
            backdrop-filter: blur(10px);
            border-top: 4px solid var(--accent-orange);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-blue);
            position: relative;
            display: inline-block;
        }

        .header h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-orange), var(--accent-green));
            border-radius: 3px;
        }

        .header p {
            color: #666;
            margin: 0 auto;
            max-width: 90%;
        }

        .campus-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .campus-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: white;
            height: 100%;
            border-left: 3px solid var(--accent-green);
        }

        .campus-card:nth-child(even) {
            border-left: 3px solid var(--accent-orange);
        }

        .campus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .campus-image-container {
            position: relative;
            height: 180px;
            overflow: hidden;
        }

        .campus-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: filter 0.3s ease;
        }

        .campus-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.8), rgba(76, 175, 80, 0.6));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .campus-image-container:hover .campus-image {
            filter: blur(3px);
        }

        .campus-image-container:hover .campus-overlay {
            opacity: 1;
        }

        .campus-details {
            padding: 15px;
            background: linear-gradient(to bottom, white, var(--light-green));
        }

        .campus-card:nth-child(even) .campus-details {
            background: linear-gradient(to bottom, white, var(--light-orange));
        }

        .campus-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-blue);
        }

        .campus-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .campus-location::before {
            content: '•';
            color: var(--accent-orange);
            margin-right: 5px;
            font-size: 1.2rem;
        }

        .campus-card:nth-child(even) .campus-location::before {
            color: var(--accent-green);
        }

        .campus-description {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .set-destination-btn {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-green));
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .campus-card:nth-child(even) .set-destination-btn {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-orange));
        }

        .set-destination-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Media queries for larger screens */
        @media (min-width: 576px) {
            .page-container {
                padding: 30px;
                max-width: 540px;
            }
            
            .header p {
                max-width: 80%;
            }
        }

        @media (min-width: 768px) {
            .page-container {
                padding: 35px;
                max-width: 720px;
            }
            
            .campus-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
            
            .campus-card {
                display: flex;
                flex-direction: column;
            }
            
            .campus-image-container {
                height: 200px;
            }
        }

        @media (min-width: 992px) {
            .page-container {
                padding: 40px;
                max-width: 960px;
            }
            
            .campus-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
            
            .campus-card {
                flex-direction: row;
                height: 220px;
            }
            
            .campus-image-container {
                width: 40%;
                height: 100%;
            }
            
            .campus-details {
                width: 60%;
                padding: 20px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }

        @media (min-width: 1200px) {
            .page-container {
                max-width: 1140px;
            }
            
            .campus-card {
                height: 240px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="header">
            <h2>Choose Your Campus</h2>
            <p>Select a destination campus to continue your journey with Lugar Lang!</p>
        </div>
        
        <div class="campus-grid">
            <!-- Campus 1 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="https://source.unsplash.com/random/400x300/?university,campus" alt="North Campus" class="campus-image">
                    <div class="campus-overlay">
                        <p>Set this campus as your destination</p>
                        <button class="set-destination-btn">Set Destination</button>
                    </div>
                </div>
                <div class="campus-details">
                    <h3 class="campus-name">Talamban Campus</h3>
                    <p class="campus-location">Stellar Heights, Orion District</p>
                    <p class="campus-description">A serene campus known for its beautiful architecture and expansive green spaces.</p>
                </div>
            </div>
            
            <!-- Campus 2 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="https://source.unsplash.com/random/400x300/?college,modern" alt="South Campus" class="campus-image">
                    <div class="campus-overlay">
                        <p>Set this campus as your destination</p>
                        <button class="set-destination-btn">Set Destination</button>
                    </div>
                </div>
                <div class="campus-details">
                    <h3 class="campus-name">Downtown Campus</h3>
                    <p class="campus-location">Lunar Valley, Nova Region</p>
                    <p class="campus-description">A modern campus with state-of-the-art facilities nestled in the heart of the technology district.</p>
                </div>
            </div>
            
            <!-- Campus 3 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="https://source.unsplash.com/random/400x300/?university,building" alt="East Campus" class="campus-image">
                    <div class="campus-overlay">
                        <p>Set this campus as your destination</p>
                        <button class="set-destination-btn">Set Destination</button>
                    </div>
                </div>
                <div class="campus-details">
                    <h3 class="campus-name">South Campus</h3>
                    <p class="campus-location">Cosmic Junction, Nebula Avenue</p>
                    <p class="campus-description">A vibrant campus with a rich cultural scene and innovative research facilities.</p>
                </div>
            </div>
            
            <!-- Campus 4 -->
            <div class="campus-card">
                <div class="campus-image-container">
                    <img src="https://source.unsplash.com/random/400x300/?campus,architecture" alt="West Campus" class="campus-image">
                    <div class="campus-overlay">
                        <p>Set this campus as your destination</p>
                        <button class="set-destination-btn">Set Destination</button>
                    </div>
                </div>
                <div class="campus-details">
                    <h3 class="campus-name">North Campus</h3>
                    <p class="campus-location">Astral Heights, Galaxy Boulevard</p>
                    <p class="campus-description">A historic campus with cutting-edge facilities and a strong focus on innovation.</p>
                </div>
            </div>
        </div>
    </div>

      <script src="campus.js"></script>
  
</body>
</html>