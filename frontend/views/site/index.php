<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Vehículos y Reservas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#10b981',
                        dark: '#1f2937',
                        light: '#f9fafb',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        success: '#10b981'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .tab-button {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .status-badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .vehicle-image {
            transition: transform 0.5s ease;
            height: 180px;
            object-fit: cover;
        }

        .vehicle-image:hover {
            transform: scale(1.05);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .connection-panel {
            transition: all 0.3s ease;
        }

        .diagnostics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .diagnostic-card {
            border-left: 4px solid;
            padding: 12px 16px;
        }

        .solution-step {
            counter-increment: step-counter;
            margin-bottom: 12px;
            padding-left: 30px;
            position: relative;
        }

        .solution-step::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: #3b82f6;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        .fixed-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-dark mb-2">Sistema de Gestión de Vehículos y Reservas</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Gestione su flota de vehículos y reservas de forma eficiente</p>
            <div class="mt-4 flex justify-center">
                <div class="inline-flex bg-white rounded-lg shadow p-1">
                    <button id="vehiclesTab" class="tab-button px-4 py-2 rounded-lg font-medium flex items-center transition-colors bg-primary text-white">
                        <i class="fas fa-car mr-2"></i>Vehículos
                    </button>
                    <button id="reservationsTab" class="tab-button px-4 py-2 rounded-lg font-medium flex items-center transition-colors text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-calendar-check mr-2"></i>Reservas
                    </button>
                </div>
            </div>
        </header>

        <!-- Vehicles Section -->
        <section id="vehiclesSection">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            id="searchVehicleInput"
                            placeholder="Buscar vehículos por modelo, marca o autonomía..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button
                        id="searchVehicleButton"
                        class="bg-primary hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-300 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Buscar Vehículos
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="vehicleResults">
                    <!-- Vehicle cards will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Reservations Section (Hidden by default) -->
        <section id="reservationsSection" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            id="searchReservationInput"
                            placeholder="Buscar reservas por ID, cliente o vehículo..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                    </div>
                    <button
                        id="searchReservationButton"
                        class="bg-secondary hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-300 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Buscar Reservas
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Reserva</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Reserva</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="reservationResults">
                            <!-- Reservation data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Connection Status Panel -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 connection-panel">
            <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                <i class="fas fa-plug mr-2"></i>Estado de la Conexión
            </h2>

            <div id="connectionStatus" class="mb-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-bold text-yellow-700">Fallo de conexión, usando datos de demostración</h3>
                        <p class="text-yellow-600">No se pudo conectar con la API, mostrando datos de demostración como alternativa.</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="font-bold text-gray-700 mb-2">Diagnóstico de Problemas:</h3>

                <div class="diagnostics-grid">
                    <div class="diagnostic-card border-blue-500 bg-blue-50">
                        <h4 class="font-semibold text-blue-700 flex items-center">
                            <i class="fas fa-link mr-2"></i>URL de Vehículos
                        </h4>
                        <div class="mt-2">
                            <code id="vehicleApiUrl" class="bg-blue-100 px-2 py-1 rounded text-sm break-all">http://reserva.com/index.php?r=vehiculo</code>
                        </div>
                    </div>

                    <div class="diagnostic-card border-purple-500 bg-purple-50">
                        <h4 class="font-semibold text-purple-700 flex items-center">
                            <i class="fas fa-link mr-2"></i>URL de Reservas
                        </h4>
                        <div class="mt-2">
                            <code id="reservationApiUrl" class="bg-purple-100 px-2 py-1 rounded text-sm break-all">http://reserva.com/index.php?r=api/reserva</code>
                        </div>
                    </div>

                    <div class="diagnostic-card border-red-500 bg-red-50">
                        <h4 class="font-semibold text-red-700 flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i>Configuración CORS
                        </h4>
                        <div class="mt-2 text-sm text-red-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Compruebe la configuración de CORS en el servidor
                        </div>
                    </div>

                    <div class="diagnostic-card border-green-500 bg-green-50">
                        <h4 class="font-semibold text-green-700 flex items-center">
                            <i class="fas fa-network-wired mr-2"></i>Problemas de Red
                        </h4>
                        <div class="mt-2 text-sm text-green-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Verifique su conexión a internet y configuraciones de red
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="font-bold text-gray-700 mb-3">Solución para Entorno WAMP:</h3>
            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <ol class="list-decimal pl-5 space-y-3">
                    <li class="solution-step">
                        <strong>Verifica que WAMP esté funcionando:</strong> El icono de WAMP en la barra de tareas debe ser verde.
                    </li>
                    <li class="solution-step">
                        <strong>Habilita CORS en tu API:</strong> Agrega estos encabezados a tu código PHP:
                        <pre class="bg-gray-800 text-white p-3 rounded mt-2 text-sm">header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");</pre>
                    </li>
                    <li class="solution-step">
                        <strong>Verifica la ruta de tu proyecto:</strong> Tu proyecto debe estar en la carpeta <code>www</code> o <code>htdocs</code> de WAMP
                    </li>
                    <li class="solution-step">
                        <strong>Prueba la API directamente:</strong> Abre estas URLs en tu navegador para ver si devuelven datos:
                        <div class="mt-2 space-y-2">
                            <div>
                                <a id="vehicleTestLink" href="#" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <i class="fas fa-car mr-2"></i>Probar API de Vehículos
                                </a>
                            </div>
                            <div>
                                <a id="reservationTestLink" href="#" target="_blank" class="text-purple-600 hover:underline flex items-center">
                                    <i class="fas fa-calendar-check mr-2"></i>Probar API de Reservas
                                </a>
                            </div>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <button id="tryAgainButton" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Reintentar conexión
                </button>
                <button id="useDemoDataButton" class="border border-primary text-primary hover:bg-blue-50 px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-database mr-2"></i>Usar datos de demostración
                </button>
            </div>

            <div id="technicalDetails" class="mt-4 p-4 bg-gray-800 text-gray-100 rounded-lg text-sm font-mono hidden">
                <div class="mb-2">
                    <span class="text-blue-400">// Detalles de la última solicitud</span>
                </div>
                <div id="requestDetails" class="whitespace-pre-wrap"></div>
            </div>
        </div>

        <!-- Error Message Container -->
        <div id="errorMessage" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-8 hidden">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-xl mt-1 mr-3 text-red-500"></i>
                <div>
                    <h3 class="font-bold" id="errorTitle"></h3>
                    <p id="errorDetail"></p>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-8 flex flex-col items-center">
                <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-gray-700 font-medium">Cargando datos...</p>
            </div>
        </div>
    </div>

    <script>
        // Mock data for demonstration
        const demoVehicles = [{
                id: 1,
                modelo: "IONIQ 5",
                marca: "Hyundai",
                autonomia: 481,
                precio_hora: 28.50,
                imagen: "ioniq5.jpg",
                disponible: 1
            },
            {
                id: 2,
                modelo: "Taycan",
                marca: "Porsche",
                autonomia: 450,
                precio_hora: 60.00,
                imagen: "taycan.png",
                disponible: 1
            },
            {
                id: 3,
                modelo: "ZC01",
                marca: "Leapmotor",
                autonomia: 400,
                precio_hora: 20.00,
                imagen: null,
                disponible: 0
            },
            {
                id: 4,
                modelo: "Ariya",
                marca: "Nissan",
                autonomia: 500,
                precio_hora: 32.00,
                imagen: "ariya.jpg",
                disponible: 1
            },
            {
                id: 5,
                modelo: "EQS",
                marca: "Mercedes-Benz",
                autonomia: 650,
                precio_hora: 55.00,
                imagen: "eqs.webp",
                disponible: 1
            }
        ];

        const demoReservations = [{
                id: 101,
                vehiculo_id: 1,
                vehiculo: "Hyundai IONIQ 5",
                cliente: "Juan Pérez",
                fecha_inicio: "2023-10-15",
                fecha_fin: "2023-10-17",
                total: 136.80,
                estado: "Confirmada"
            },
            {
                id: 102,
                vehiculo_id: 2,
                vehiculo: "Porsche Taycan",
                cliente: "María García",
                fecha_inicio: "2023-10-18",
                fecha_fin: "2023-10-20",
                total: 360.00,
                estado: "Completada"
            },
            {
                id: 103,
                vehiculo_id: 5,
                vehiculo: "Mercedes-Benz EQS",
                cliente: "Carlos Rodríguez",
                fecha_inicio: "2023-10-22",
                fecha_fin: "2023-10-25",
                total: 660.00,
                estado: "Pendiente"
            }
        ];

        // DOM Elements
        const vehiclesTab = document.getElementById('vehiclesTab');
        const reservationsTab = document.getElementById('reservationsTab');
        const vehiclesSection = document.getElementById('vehiclesSection');
        const reservationsSection = document.getElementById('reservationsSection');
        const searchVehicleInput = document.getElementById('searchVehicleInput');
        const searchVehicleButton = document.getElementById('searchVehicleButton');
        const searchReservationInput = document.getElementById('searchReservationInput');
        const searchReservationButton = document.getElementById('searchReservationButton');
        const vehicleResults = document.getElementById('vehicleResults');
        const reservationResults = document.getElementById('reservationResults');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const errorTitle = document.getElementById('errorTitle');
        const errorDetail = document.getElementById('errorDetail');
        const tryAgainButton = document.getElementById('tryAgainButton');
        const useDemoDataButton = document.getElementById('useDemoDataButton');
        const connectionStatus = document.getElementById('connectionStatus');
        const vehicleApiUrl = document.getElementById('vehicleApiUrl');
        const reservationApiUrl = document.getElementById('reservationApiUrl');
        const vehicleTestLink = document.getElementById('vehicleTestLink');
        const reservationTestLink = document.getElementById('reservationTestLink');
        const technicalDetails = document.getElementById('technicalDetails');
        const requestDetails = document.getElementById('requestDetails');

        // State variables
        let useDemoData = true;
        let lastRequestInfo = null;
        let connectionAttempts = 0;
        const MAX_ATTEMPTS = 3;
        let globalVehicles = []; // Para almacenar vehículos y usarlos en reservas

        // URLs de las APIs (según lo especificado)
        const VEHICLE_API_URL = 'http://reserva.com/index.php?r=vehiculo';
        const RESERVATION_API_URL = 'http://reserva.com/index.php/api/reserva?verification_token=admin';

        // Set API URLs in UI
        vehicleApiUrl.textContent = VEHICLE_API_URL;
        reservationApiUrl.textContent = RESERVATION_API_URL;
        vehicleTestLink.href = VEHICLE_API_URL;
        reservationTestLink.href = RESERVATION_API_URL;

        // Tab switching
        vehiclesTab.addEventListener('click', () => {
            vehiclesTab.classList.add('bg-primary', 'text-white');
            vehiclesTab.classList.remove('text-gray-600', 'hover:bg-gray-100');
            reservationsTab.classList.remove('bg-secondary', 'text-white');
            reservationsTab.classList.add('text-gray-600', 'hover:bg-gray-100');
            vehiclesSection.classList.remove('hidden');
            reservationsSection.classList.add('hidden');
            searchVehicles(searchVehicleInput.value.trim());
        });

        reservationsTab.addEventListener('click', () => {
            reservationsTab.classList.add('bg-secondary', 'text-white');
            reservationsTab.classList.remove('text-gray-600', 'hover:bg-gray-100');
            vehiclesTab.classList.remove('bg-primary', 'text-white');
            vehiclesTab.classList.add('text-gray-600', 'hover:bg-gray-100');
            reservationsSection.classList.remove('hidden');
            vehiclesSection.classList.add('hidden');
            searchReservations(searchReservationInput.value.trim());
        });

        // Search Vehicles
        searchVehicleButton.addEventListener('click', () => {
            const query = searchVehicleInput.value.trim();
            searchVehicles(query);
        });

        searchVehicleInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = searchVehicleInput.value.trim();
                searchVehicles(query);
            }
        });

        // Search Reservations
        searchReservationButton.addEventListener('click', () => {
            const query = searchReservationInput.value.trim();
            searchReservations(query);
        });

        searchReservationInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = searchReservationInput.value.trim();
                searchReservations(query);
            }
        });

        // Try again button
        tryAgainButton.addEventListener('click', () => {
            connectionAttempts = 0;
            useDemoData = false;
            updateConnectionStatus('reconnecting');
            searchVehicles(searchVehicleInput.value.trim());
            searchReservations(searchReservationInput.value.trim());
        });

        // Use demo data button
        useDemoDataButton.addEventListener('click', () => {
            useDemoData = true;
            updateConnectionStatus('demo');
            searchVehicles(searchVehicleInput.value.trim());
            searchReservations(searchReservationInput.value.trim());
        });

        // Search vehicles function
        async function searchVehicles(query = '') {
            showLoading();
            hideError();
            connectionAttempts++;

            try {
                let vehicles;

                if (useDemoData) {
                    vehicles = filterVehicles(demoVehicles, query);
                    displayVehicles(vehicles);
                    updateConnectionStatus('demo');
                } else {
                    // Store request info for debugging
                    lastRequestInfo = `Solicitud GET a: ${VEHICLE_API_URL}\nHora: ${new Date().toLocaleTimeString()}\nParámetros: ${query ? `search=${query}` : 'ninguno'}`;

                    const response = await fetch(VEHICLE_API_URL);

                    if (!response.ok) {
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                    }

                    vehicles = await response.json();
                    vehicles = filterVehicles(vehicles, query);
                    displayVehicles(vehicles);
                    updateConnectionStatus('connected');
                    connectionAttempts = 0; // Reset attempts on success
                }

                // Guardar vehículos para usar en reservas
                globalVehicles = vehicles;
            } catch (error) {
                console.error('Error obteniendo vehículos:', error);
                showError('Error de conexión', `Detalles: ${error.message}`);

                // Auto fallback to demo data if API fails
                if (connectionAttempts >= MAX_ATTEMPTS) {
                    useDemoData = true;
                    const filtered = filterVehicles(demoVehicles, query);
                    displayVehicles(filtered);
                    globalVehicles = filtered;
                    updateConnectionStatus('fallback_demo');
                } else {
                    updateConnectionStatus('error');
                }
            } finally {
                hideLoading();
            }
        }

        // Search reservations function - CORRECCIÓN APLICADA
        async function searchReservations(query = '') {
            showLoading();
            hideError();

            try {
                let reservations;

                if (useDemoData) {
                    reservations = filterReservations(demoReservations, query);
                    displayReservations(reservations);
                    updateConnectionStatus('demo');
                } else {
                    // Store request info for debugging
                    lastRequestInfo = `Solicitud GET a: ${RESERVATION_API_URL}\nHora: ${new Date().toLocaleTimeString()}\nParámetros: ${query ? `search=${query}` : 'ninguno'}`;

                    const response = await fetch(RESERVATION_API_URL, {
                        headers: {
                            'Accept': 'application/json' // Especificamos que queremos JSON
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                    }

                    // Parseamos la respuesta como JSON
                    const data = await response.json();

                    // Verificamos que exista el array de reservas
                    if (!data.reservas || !Array.isArray(data.reservas)) {
                        throw new Error('Formato de respuesta inválido: no se encontró el array "reservas"');
                    }

                    // Procesamos cada reserva
                    reservations = data.reservas.map(reserva => {
                        // Buscar el vehículo en la lista global
                        const vehiculo = globalVehicles.find(v => v.id == reserva.vehiculo_id);
                        const nombreVehiculo = vehiculo ?
                            `${vehiculo.marca} ${vehiculo.modelo}` :
                            `Vehículo ID: ${reserva.vehiculo_id}`;

                        return {
                            id: reserva.id,
                            cliente: `${reserva.nombre} ${reserva.apellido}`,
                            vehiculo: nombreVehiculo,
                            fecha_reserva: reserva.fecha_formateada || reserva.fecha_reserva,
                            contacto: `${reserva.email} / ${reserva.telefono}`,
                            estado: "Confirmada", // Por defecto
                            raw: {
                                nombre: reserva.nombre,
                                apellido: reserva.apellido,
                                rut: reserva.rut,
                                email: reserva.email,
                                telefono: reserva.telefono,
                                vehiculo_id: reserva.vehiculo_id,
                                fecha_reserva: reserva.fecha_reserva
                            }
                        };
                    });

                    reservations = filterReservations(reservations, query);
                    displayReservations(reservations);
                    updateConnectionStatus('connected');
                }
            } catch (error) {
                console.error('Error obteniendo reservas:', error);
                showError('Error de conexión', `Detalles: ${error.message}`);

                // Auto fallback to demo data if API fails
                useDemoData = true;
                displayReservations(filterReservations(demoReservations, query));
                updateConnectionStatus('fallback_demo');

                // Mostrar detalles técnicos para depuración
                requestDetails.textContent = `${lastRequestInfo}\n\nError: ${error.message}`;
                technicalDetails.classList.remove('hidden');
            } finally {
                hideLoading();
            }
        }

        // Filter vehicles based on query
        function filterVehicles(vehicles, query) {
            if (!query) return vehicles;

            return vehicles.filter(vehicle => {
                const searchText = query.toLowerCase();
                return (
                    (vehicle.modelo && vehicle.modelo.toLowerCase().includes(searchText)) ||
                    (vehicle.marca && vehicle.marca.toLowerCase().includes(searchText)) ||
                    (vehicle.autonomia && vehicle.autonomia.toString().includes(searchText))
                );
            });
        }

        // Filter reservations based on query
        function filterReservations(reservations, query) {
            if (!query) return reservations;

            return reservations.filter(reservation => {
                const searchText = query.toLowerCase();
                return (
                    reservation.id.toString().includes(searchText) ||
                    (reservation.vehiculo && reservation.vehiculo.toLowerCase().includes(searchText)) ||
                    (reservation.cliente && reservation.cliente.toLowerCase().includes(searchText))
                );
            });
        }

        // Display vehicles
        function displayVehicles(vehicles) {
            if (vehicles.length === 0) {
                vehicleResults.innerHTML = `
                    <div class="col-span-full text-center py-10">
                        <i class="fas fa-car text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600">No se encontraron vehículos</h3>
                        <p class="text-gray-500 mt-2">Intente con otros términos de búsqueda.</p>
                    </div>
                `;
                return;
            }

            vehicleResults.innerHTML = '';

            vehicles.forEach(vehicle => {
                const vehicleCard = document.createElement('div');
                vehicleCard.className = 'card fade-in';

                vehicleCard.innerHTML = `
                    <div class="overflow-hidden">
                        ${vehicle.imagen ? 
                            `<img src="${vehicle.imagen}" alt="${vehicle.marca} ${vehicle.modelo}" class="vehicle-image w-full" onerror="this.onerror=null;this.src='https://placehold.co/600x400/aabbcc/ffffff?text=No+Imagen';">` : 
                            `<div class="bg-gray-200 w-full h-48 flex items-center justify-center">
                                <i class="fas fa-car text-4xl text-gray-400"></i>
                            </div>`
                        }
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-800">${vehicle.marca} ${vehicle.modelo}</h3>
                            <span class="status-badge ${vehicle.disponible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${vehicle.disponible ? 'Disponible' : 'No disponible'}
                            </span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-500">Autonomía</p>
                                <p class="font-medium">${vehicle.autonomia} km</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Precio/hora</p>
                                <p class="font-medium">${parseFloat(vehicle.precio_hora).toFixed(2)} €</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <button class="w-full bg-primary hover:bg-blue-700 text-white py-2 rounded-lg transition flex items-center justify-center">
                                <i class="far fa-calendar-plus mr-2"></i>Reservar
                            </button>
                        </div>
                    </div>
                `;
                vehicleResults.appendChild(vehicleCard);
            });
        }

        // Display reservations
        function displayReservations(reservations) {
            if (reservations.length === 0) {
                reservationResults.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center justify-center py-8">
                                <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-600">No se encontraron reservas</h3>
                                <p class="text-gray-500">Intente con otros términos de búsqueda.</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            reservationResults.innerHTML = '';

            reservations.forEach(reservation => {
                const row = document.createElement('tr');
                row.className = 'fade-in';

                // Determine status color
                let statusColor = 'bg-blue-100 text-blue-800'; // Default for 'Pendiente'
                if (reservation.estado === 'Confirmada') statusColor = 'bg-green-100 text-green-800';
                if (reservation.estado === 'Completada') statusColor = 'bg-gray-100 text-gray-800';
                if (reservation.estado === 'Cancelada') statusColor = 'bg-red-100 text-red-800';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap font-medium">#${reservation.id}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium">${reservation.cliente || 'N/A'}</div>
                        <div class="text-sm text-gray-500">${reservation.raw?.rut || ''}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">${reservation.vehiculo || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${reservation.fecha_reserva || 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">${reservation.contacto || 'N/A'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">
                            ${reservation.estado || 'N/A'}
                        </span>
                    </td>
                `;
                reservationResults.appendChild(row);
            });
        }

        // Loading functions
        function showLoading() {
            loadingSpinner.classList.remove('hidden');
        }

        function hideLoading() {
            loadingSpinner.classList.add('hidden');
        }

        // Error handling functions
        function showError(title, detail) {
            errorTitle.textContent = title;
            errorDetail.textContent = detail;
            errorMessage.classList.remove('hidden');
        }

        function hideError() {
            errorMessage.classList.add('hidden');
        }

        // Update connection status panel
        function updateConnectionStatus(status) {
            connectionStatus.classList.remove(
                'bg-red-50', 'border-red-200', 'text-red-700',
                'bg-green-50', 'border-green-200', 'text-green-700',
                'bg-blue-50', 'border-blue-200', 'text-blue-700',
                'bg-orange-50', 'border-orange-200', 'text-orange-700',
                'bg-yellow-50', 'border-yellow-200', 'text-yellow-700'
            );

            let icon = 'fas fa-plug';
            let title = '';
            let message = '';
            let statusClass = '';

            if (status === 'connected') {
                statusClass = 'bg-green-50 border-green-200 text-green-700';
                icon = 'fas fa-check-circle';
                title = 'Conexión exitosa';
                message = 'Conectado con la API correctamente.';
            } else if (status === 'demo') {
                statusClass = 'bg-blue-50 border-blue-200 text-blue-700';
                icon = 'fas fa-database';
                title = 'Modo demostración activado';
                message = 'Está viendo datos de demostración.';
            } else if (status === 'reconnecting') {
                statusClass = 'bg-orange-50 border-orange-200 text-orange-700';
                icon = 'fas fa-sync-alt';
                title = 'Reintentando conexión...';
                message = 'Intentando conectar con el servidor backend.';
            } else if (status === 'fallback_demo') {
                statusClass = 'bg-yellow-50 border-yellow-200 text-yellow-700';
                icon = 'fas fa-exclamation-triangle';
                title = 'Fallo de conexión, usando datos de demostración';
                message = 'No se pudo conectar con la API, mostrando datos de demostración como alternativa.';
            } else { // Default to error
                statusClass = 'bg-red-50 border-red-200 text-red-700';
                icon = 'fas fa-exclamation-triangle';
                title = 'Error de conexión con la API';
                message = 'No se pudo establecer conexión con el servidor backend.';
            }

            connectionStatus.className = `mb-4 p-4 rounded-lg ${statusClass}`;
            connectionStatus.innerHTML = `
                <div class="flex items-center">
                    <i class="${icon} text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-bold">${title}</h3>
                        <p>${message}</p>
                    </div>
                </div>
            `;
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', () => {
            updateConnectionStatus('fallback_demo');
            searchVehicles();
        });
    </script>
</body>

</html>