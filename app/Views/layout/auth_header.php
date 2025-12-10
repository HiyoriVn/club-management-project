<!DOCTYPE html>
<html lang="vi" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'CLB Management'; ?></title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <style>
        /* Subtle Pattern Background */
        .auth-background {
            background-color: #0f172a;
            /* slate-900 */
            background-image:
                radial-gradient(at 40% 20%, hsla(217, 91%, 60%, 0.3) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(262, 91%, 60%, 0.3) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(217, 91%, 60%, 0.2) 0px, transparent 50%),
                radial-gradient(at 80% 50%, hsla(262, 91%, 60%, 0.2) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(217, 91%, 60%, 0.3) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(262, 91%, 60%, 0.3) 0px, transparent 50%);
        }

        /* Floating particles */
        @keyframes float-particles {

            0%,
            100% {
                transform: translateY(0px) translateX(0px);
            }

            33% {
                transform: translateY(-20px) translateX(10px);
            }

            66% {
                transform: translateY(10px) translateX(-10px);
            }
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float-particles 8s ease-in-out infinite;
        }

        /* Glass morphism effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        /* Logo pulse animation */
        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
            }

            50% {
                box-shadow: 0 0 40px rgba(99, 102, 241, 0.8);
            }
        }

        .logo-container {
            animation: pulse-glow 3s ease-in-out infinite;
        }
    </style>
</head>

<body class="h-full animated-gradient">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">