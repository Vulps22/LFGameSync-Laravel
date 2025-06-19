<?php
// roadmap.php

// This line includes the Composer autoloader, necessary if you're using phpdotenv.
// It should be placed at the very top of your script.
require_once __DIR__ . '/vendor/autoload.php';

// This initializes phpdotenv to load environment variables from your .env file.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the GitHub PAT from the loaded environment variables.
$github_pat = $_ENV['GITHUB_READ_PROJECT_PAT'] ?? null;

// --- Configuration ---
// Your GitHub ProjectV2 Node ID.
// This is the specific ID you fetched: PVT_kwHOACitxM4A7fSZ
define('GITHUB_PROJECT_NODE_ID', 'PVT_kwHOACitxM4A7fSZ');

// GitHub GraphQL API Endpoint
define('GITHUB_API_URL', 'https://api.github.com/graphql');

// Path to the cacert.pem file.
// IMPORTANT: Update this path to where you saved your cacert.pem file!
// Example for Windows: 'C:\php\extras\ssl\cacert.pem'
// Example for Linux: '/etc/ssl/certs/cacert.pem' or '/usr/local/etc/openssl/cert.pem'
// Or within your project: __DIR__ . '/certs/cacert.pem' (if you put it in a 'certs' subfolder)
define('CA_CERT_PATH', __DIR__ . '/certs/cacert.pem'); // <--- ADJUST THIS PATH

// Column names as they appear on your GitHub Project Board.
// These are used for mapping fetched data to display columns.
// ENSURE THESE KEYS EXACTLY MATCH YOUR GITHUB PROJECT BOARD COLUMN NAMES (case-sensitive).
// Keys are the exact values from GitHub, values are for display on your website.
$github_column_map = [
    'Backlog'           => 'Backlog',
    'Ready'             => 'Ready',
    'In progress'       => 'In Progress',       // GitHub: "In progress" -> Display: "In Progress"
    'In review'         => 'In Review',         // GitHub: "In review" -> Display: "In Review"
    'Ready and Waiting' => 'Ready & Waiting',   // GitHub: "Ready and Waiting" -> Display: "Ready & Waiting"
    'Done'              => 'Last Release'       // GitHub: "Done" -> Display: "Last Release"
];

// Define column icons and colors for display.
// THESE KEYS MUST MATCH THE *DISPLAY* NAMES (values from $github_column_map) or any unknown status.
$column_display_config = [
    'Backlog'         => ['icon' => 'fa-box-archive', 'color' => 'text-slate-400', 'subtext' => '(no particular order)'],
    'Ready'           => ['icon' => 'fa-play-circle', 'color' => 'text-green-400', 'subtext' => ''],
    'In Progress'     => ['icon' => 'fa-hourglass-half', 'color' => 'text-amber-400', 'subtext' => ''],
    'In Review'       => ['icon' => 'fa-magnifying-glass', 'color' => 'text-sky-400', 'subtext' => ''],
    'Ready & Waiting' => ['icon' => 'fa-check-double', 'color' => 'text-emerald-400', 'subtext' => ''],
    'Last Release'    => ['icon' => 'fa-paper-plane', 'color' => 'text-green-500', 'subtext' => ''],
    'Unknown Column'  => ['icon' => 'fa-circle-question', 'color' => 'text-gray-400', 'subtext' => '(unmapped column)'], // Added for debugging
];

// Function to determine best text color (white or black) based on background luminance
// This function remains in PHP to support the fallback inline styling for UNKNOWN hex codes.
function getContrastColor_internal($hexcolor) {
    // Remove # if present
    $hexcolor = str_replace('#', '', $hexcolor);
    // Handle 3-digit hex codes
    if (strlen($hexcolor) == 3) {
        $hexcolor = str_repeat(substr($hexcolor, 0, 1), 2) . str_repeat(substr($hexcolor, 1, 1), 2) . str_repeat(substr($hexcolor, 2, 1), 2);
    }
    $r = hexdec(substr($hexcolor, 0, 2));
    $g = hexdec(substr($hexcolor, 2, 2));
    $b = hexdec(substr($hexcolor, 4, 2));

    // Calculate luminance (per W3C accessibility guidelines)
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

    // Use a dark color for light backgrounds, or a light color for dark backgrounds
    return ($luminance > 0.5) ? '#1A202C' : '#F7FAFC'; // Tailwind slate-900 or white-ish
}

// Define the known GitHub label hex colors and their corresponding CSS class suffixes.
// This array is used in the PHP to map labels to their class names.
// Note: Hex codes are lowercase for consistency.
$known_label_colors_map = [ // Renamed to avoid conflict with function name if it existed
    'a2eeef' => 'enhancement',
    'd876e3' => 'premium-candidate',
    'b60205' => 'bug-critical',
    'fef2c0' => 'docs-help',
    '0052cc' => 'feature',
    'ededed' => 'cleanup-general',
    'cc317c' => 'release-urgent',
    '7057ff' => 'good-first-issue',
    'c85e3b' => 'cleanup-aisle-10', // This is the orange one you wanted to customize
    'aaaaaa' => 'not-urgent',
    '36851f' => 'release-ready',
    'c869f5' => 'premium',
    '0075ca' => 'documentation',
    'd73a4a' => 'bug',
];


// Initialize variables before fetching data
$response_data = null;
$error_message = null; // Will be populated if PAT or cURL fails
$last_updated_timestamp = 'Unknown';
$project_name = 'LFGameSync Roadmap';

// --- Fetch data from GitHub GraphQL API ---
if (!$github_pat) {
    // This check is now here to set the error_message early if PAT is missing.
    error_log("GITHUB_READ_PROJECT_PAT environment variable not set. Please ensure it's configured on your server.");
    $error_message = "GitHub Personal Access Token (PAT) is not configured. Cannot fetch roadmap data.";
} else {
    $graphql_query = '
    query GetProjectRoadmap($projectId: ID!) {
      node(id: $projectId) {
        ... on ProjectV2 {
          title
          updatedAt
          items(first: 100) { # Adjust \'first\' if you expect more than 100 items in total
            nodes {
              id
              fieldValues(first: 10) { # Get field values for the item
                nodes {
                  ... on ProjectV2ItemFieldSingleSelectValue {
                    field {
                      ... on ProjectV2Field {
                        name # This would be "Status" if the field itself was named
                      }
                    }
                    name # This is the actual value of the single select field (e.g., "Backlog", "In progress")
                  }
                }
              }
              content {
                ... on Issue {
                  title
                  url
                  number
                  body
                  labels(first: 10) {
                    nodes {
                      name
                      color
                    }
                  }
                }
                ... on PullRequest {
                  title
                  url
                  number
                  body
                  labels(first: 10) {
                    nodes {
                      name
                      color
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    ';

    $post_fields = json_encode([
        'query'     => $graphql_query,
        'variables' => ['projectId' => GITHUB_PROJECT_NODE_ID]
    ]);

    // Initialize cURL session
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, GITHUB_API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: bearer ' . $github_pat,
        'Content-Type: application/json',
        'User-Agent: LFGameSync-Roadmap-Integrator' // GitHub requires a User-Agent
    ]);

    // SSL certificate verification settings
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_CAINFO, CA_CERT_PATH);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_message = 'cURL Error: ' . curl_error($ch);
        error_log($error_message);
    } else {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code !== 200) {
            $error_message = "GitHub API returned HTTP code: {$http_code}. Response: " . $response;
            error_log($error_message);
        } else {
            $response_data = json_decode($response, true);

            if (isset($response_data['errors'])) {
                $error_message = "GraphQL Errors: " . json_encode($response_data['errors']);
                error_log($error_message);
            } else if (!isset($response_data['data']['node'])) {
                $error_message = "Unexpected GitHub API response structure or node not found.";
                error_log($error_message);
            }
        }
    }
    curl_close($ch);
}

// --- Process fetched data ---
$roadmap_columns = [];
// Initialize all expected display columns to ensure they always appear, even if empty
foreach ($github_column_map as $github_col_name => $display_col_name) {
    $roadmap_columns[$display_col_name] = [];
}

if ($response_data && isset($response_data['data']['node'])) {
    $project = $response_data['data']['node'];
    $project_name = $project['title'];
    $last_updated_timestamp = date('F j, Y, g:i a T', strtotime($project['updatedAt']));

    foreach ($project['items']['nodes'] as $item) {
        $status = 'Unknown Column'; // Default if status field isn't found for an item
        $found_status_field = false;

        // Iterate through all field values for the current item to find its 'Status'
        foreach ($item['fieldValues']['nodes'] as $fieldValue) {
            // As per var_dump, 'name' in fieldValue directly holds the status string like "In review"
            // We're looking for a field that has a 'name' which matches one of our expected GitHub column names.
            // Assuming the 'Status' field is the primary single-select field for columns.
            if (isset($fieldValue['name']) && in_array($fieldValue['name'], array_keys($github_column_map))) {
                $status = $fieldValue['name'];
                $found_status_field = true;
                break; // Found the status field, no need to check other field values for this item
            }
        }

        // If a status field was found, map it to our desired display name
        $display_column_name = $github_column_map[$status] ?? 'Unknown Column';

        // Ensure the mapped display column exists, then add the card
        if (isset($roadmap_columns[$display_column_name])) {
            if ($item['content']) {
                $card_description = '';
                if (!empty($item['content']['body'])) {
                    // Use a regular expression to find text within {{...}} at the beginning of the string
                    if (preg_match('/^\{\{(.*?)\}\}/s', $item['content']['body'], $matches)) {
                        // If a match is found, use the captured text
                        $card_description = $matches[1];
                    } else {
                        // If no curly bracket text is found, set description to empty
                        $card_description = '';
                    }
                }

                $card = [
                    'title' => $item['content']['title'],
                    'url' => $item['content']['url'],
                    'description' => $card_description, // Use the processed description
                    'labels' => []
                ];

                if (isset($item['content']['labels']['nodes'])) {
                    foreach ($item['content']['labels']['nodes'] as $label) {
                        // Ensure hex is lowercase and no '#' for class suffix
                        $card['labels'][] = [
                            'name' => $label['name'],
                            'color' => strtolower(str_replace('#', '', $label['color']))
                        ];
                    }
                }
                $roadmap_columns[$display_column_name][] = $card;
            }
        } else {
            // If the GitHub status name is valid but not explicitly mapped in $github_column_map,
            // or if 'Unknown Column' somehow isn't initialized, handle it.
            // For now, it will fall into 'Unknown Column' by default via the $github_column_map[$status] ?? 'Unknown Column'
            // and this branch only logs unexpected status names.
            error_log("GitHub column name '{$status}' found but not mapped in \$github_column_map.");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project_name); ?> - Roadmap</title>
    <meta name="description" content="The development roadmap for LFGameSync. See what's coming next!">
    <meta name="author" content="Vulps Development">
    <meta name="theme-color" content="#0F172A">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="<?php echo htmlspecialchars($project_name); ?> - Roadmap">
    <meta property="og:site_name" content="LFGameSync">
    <meta property="og:description" content="The development roadmap for LFGameSync. See what's coming next!">
    <meta property="og:type" content="website" />
    <meta property="og:image" content="https://lfgamesync.co.uk/img/banner.png">
    <meta property="og:url" content="https://lfgamesync.co.uk/roadmap.php"> <!-- Updated to .php -->

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($project_name); ?> - Roadmap">
    <meta name="twitter:description" content="The development roadmap for LFGameSync. See what's coming next!">
    <meta name="twitter:image" content="./img/player_select.png"> <!-- Changed to player_select.png for Twitter -->

    <link rel="apple-touch-icon" href="./img/lfgamesync-2.png">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <script src="https://kit.fontawesome.com/ac7deee7ba.js" crossorigin="anonymous"></script>

    <link rel="icon" href="./img/lfgamesync-2.png" type="image/png">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar for horizontal overflow */
        .overflow-x-scroll::-webkit-scrollbar {
            height: 10px;
        }

        .overflow-x-scroll::-webkit-scrollbar-track {
            background: #1E293B;
            /* slate-800 */
            border-radius: 5px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb {
            background: #475569;
            /* slate-600 */
            border-radius: 5px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748B;
            /* slate-500 */
        }

        /* HARDCODED LABEL STYLES - YOU CAN CUSTOMIZE THESE HEX CODES DIRECTLY */
        /* Each class name corresponds to the label's hex color from GitHub API, e.g., 'label-a2eeef' for #a2eeef */
        .label-a2eeef { /* enhancement */
            background-color: #a2eeef;
            color: #1A202C; /* Dark text for contrast */
        }
        .label-d876e3 { /* premium-candidate */
            background-color: #d876e3;
            color: #1A202C; /* Dark text for contrast */
        }
        .label-b60205 { /* bug-critical */
            background-color: #b60205;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-fef2c0 { /* docs-help */
            background-color: #fef2c0;
            color: #1A202C; /* Dark text for contrast */
        }
        .label-0052cc { /* feature */
            background-color: #0052cc;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-ededed { /* cleanup-general */
            background-color: #ededed;
            color: #1A202C; /* Dark text for contrast */
        }
        .label-cc317c { /* release-urgent */
            background-color: #cc317c;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-7057ff { /* good-first-issue */
            background-color: #7057ff;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-c85e3b { /* cleanup-aisle-10 - This is the one you wanted to customize */
            background-color: #c85e3b; /* Set your desired darker orange hex here, e.g., #a04a2c */
            color: #F7FAFC; /* Light text for contrast, adjust if needed */
        }
        .label-aaaaaa { /* not-urgent */
            background-color: #aaaaaa;
            color: #1A202C; /* Dark text for contrast */
        }
        .label-36851f { /* release-ready */
            background-color: #36851f;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-c869f5 { /* premium */
            background-color: #c869f5;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-0075ca { /* documentation */
            background-color: #0075ca;
            color: #F7FAFC; /* Light text for contrast */
        }
        .label-d73a4a { /* bug */
            background-color: #d73a4a;
            color: #F7FAFC; /* Light text for contrast */
        }

        /* Style for vertical scrolling within columns */
        .column-content-scroll {
            max-height: 48vh; /* Adjusted max-height to 50% of viewport height */
            min-height: 250px; /* Added a minimum height to prevent collapse when empty */
            overflow-y: auto;
            padding-right: 1rem; /* Add padding to prevent scrollbar from obscuring content */
        }

        /* Custom scrollbar for vertical overflow */
        .column-content-scroll::-webkit-scrollbar {
            width: 8px; /* thinner vertical scrollbar */
        }

        .column-content-scroll::-webkit-scrollbar-track {
            background: #1E293B; /* slate-800 */
            border-radius: 4px;
        }

        .column-content-scroll::-webkit-scrollbar-thumb {
            background: #475569; /* slate-600 */
            border-radius: 4px;
        }

        .column-content-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748B; /* slate-500 */
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-300 antialiased">

    <header class="bg-slate-900/70 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-800">
        <nav class="mx-auto px-6 py-4 flex justify-between items-center">
            <a href="./index.html" class="text-2xl font-bold text-white tracking-tight">
                LFGameSync
            </a>
            <div class="flex items-center gap-x-4">
                <a href="https://github.com/Vulps22/project-gamer" target="_blank"
                    class="text-slate-400 hover:text-white transition-colors text-2xl">
                    <i class="fa-brands fa-github"></i>
                </a>
                <a href="https://discord.gg/PxjD25Cc85"
                    class="inline-flex items-center bg-green-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-green-500 transition-colors shadow-lg shadow-green-600/20">
                    <i class="fa-solid fa-users mr-2"></i>
                    Join the Community
                </a>
                <a href="https://discord.com/oauth2/authorize?client_id=1137177567745548318"
                    class="inline-flex items-center bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-500 transition-colors shadow-lg shadow-blue-600/20">
                    <i class="fa-brands fa-discord mr-2"></i>
                    Add to Your Server
                </a>
            </div>
        </nav>
    </header>

    <main>
        <section class="py-24 sm:py-32 bg-slate-800/50">
            <div class="container mx-auto px-6 text-center">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight">
                    LFGameSync <span class="text-blue-400">Roadmap</span>
                </h1>
                <p class="mt-6 text-lg md:text-xl max-w-3xl mx-auto text-slate-400">
                    See what's next for LFGameSync! Our public development roadmap is directly sourced from our GitHub Project board.
                </p>
                <p class="mt-4 text-slate-500 text-sm">
                    Last updated: <span id="last-updated"><?php echo htmlspecialchars($last_updated_timestamp); ?></span>
                </p>
                <?php if ($error_message): ?>
                <div class="mt-8 p-4 bg-red-800/30 border border-red-500 text-red-300 rounded-lg max-w-2xl mx-auto">
                    <p class="font-bold">Error loading roadmap:</p>
                    <p class="text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                    <p class="text-xs mt-2">Please ensure your GITHUB_READ_PROJECT_PAT is correctly set in your server's environment variables and has the 'read:project' scope.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Roadmap Section -->
        <section class="py-16">
            <!-- Outer wrapper for horizontal scrolling -->
            <div class="overflow-x-scroll pb-4 scroll-smooth">
                <!-- Flex container for columns. Now responsible for centering and padding. -->
                <div class="flex flex-nowrap justify-center gap-6 min-w-max mx-auto px-6">
                    <?php foreach ($roadmap_columns as $display_col_name => $cards): ?>
                    <?php
                        // Get config for the current column, default if not found
                        $col_config = $column_display_config[$display_col_name] ?? [
                            'icon' => 'fa-circle-question', // Default icon
                            'color' => 'text-gray-400',       // Default color
                            'subtext' => ''
                        ];
                    ?>
                    <div class="w-80 flex-shrink-0 bg-slate-800/80 rounded-lg p-4 border border-slate-700 shadow-xl">
                        <h2 class="text-xl font-bold text-white mb-4 flex items-baseline gap-2">
                            <i class="fa-solid <?php echo htmlspecialchars($col_config['icon']); ?> <?php echo htmlspecialchars($col_config['color']); ?>"></i>
                            <?php echo htmlspecialchars($display_col_name); ?>
                            <?php if (!empty($col_config['subtext'])): ?>
                            <span class="text-xs text-slate-500 font-normal ml-1 whitespace-nowrap"><?php echo htmlspecialchars($col_config['subtext']); ?></span>
                            <?php endif; ?>
                        </h2>
                        <!-- Vertical scrolling content area for the column -->
                        <div class="space-y-3 column-content-scroll">
                            <?php if (empty($cards)): ?>
                            <div class="text-center text-slate-500 italic py-4">
                                <?php
                                    // Custom empty messages for specific columns
                                    if ($display_col_name === 'In Review') {
                                        echo 'No items currently in review.';
                                    } elseif ($display_col_name === 'Ready & Waiting') {
                                        echo 'No items currently ready for deployment.';
                                    } else {
                                        echo 'No items in this column.';
                                    }
                                ?>
                            </div>
                            <?php else: ?>
                                <?php foreach ($cards as $card): ?>
                                <a href="<?php echo htmlspecialchars($card['url']); ?>" target="_blank"
                                    class="block bg-slate-900 rounded-lg p-3 border border-slate-700 hover:border-blue-500 transition-colors shadow-md">
                                    <h3 class="font-semibold text-slate-200"><?php echo htmlspecialchars($card['title']); ?></h3>
                                    <?php if (!empty($card['labels'])): ?>
                                    <div class="flex flex-wrap gap-1 mt-1 text-xs">
                                        <?php foreach ($card['labels'] as $label): ?>
                                            <?php
                                                // Convert hex color to a clean class name suffix
                                                $clean_hex = str_replace('#', '', strtolower($label['color']));
                                                $label_class_suffix = $known_label_colors_map[$clean_hex] ?? null;

                                                $label_classes = '';
                                                $inline_style = '';

                                                if ($label_class_suffix) {
                                                    // Use the hardcoded CSS class name
                                                    $label_classes = "label-{$label_class_suffix}";
                                                } else {
                                                    // Fallback to inline style for unknown hex codes
                                                    $bg_color = '#' . $clean_hex;
                                                    $text_color = getContrastColor_internal($clean_hex); // Use the internal PHP function for unknown colors
                                                    $inline_style = "background-color: {$bg_color}; color: {$text_color};";
                                                }
                                            ?>
                                            <span class="px-2 py-0.5 rounded-full <?php echo htmlspecialchars($label_classes); ?>"
                                                  <?php if (!empty($inline_style)): ?> style="<?php echo htmlspecialchars($inline_style); ?>" <?php endif; ?>>
                                                <?php echo htmlspecialchars($label['name']); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($card['description'])): ?>
                                    <p class="mt-2 text-slate-400 text-sm"><?php echo htmlspecialchars($card['description']); ?></p>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>

    <footer class="border-t border-slate-800">
        <div class="ml-3 mr-6 px-6 py-8 grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <p id="disclaimer" class="md:col-span-4 text-xs text-slate-500 text-center md:text-left">
                <sup>1</sup>LFGameSync and its developers are not affiliated with, endorsed by, or in any way officially
                connected with any of the supported stores. All product and company names are trademarks™ or registered®
                trademarks of their respective holders. We utilize publicly available data from their websites to
                provide functionality for the bot.
            </p>

            <div class="md:col-span-4 md:col-start-5 text-center text-slate-400">
                <p>&copy; 2025 LFGameSync. Built by <a href="https://vulps.co.uk" target="_blank"
                        class="underline hover:text-white transition-colors">Vulps Development.</a></p>
                <div class="mt-4 flex justify-center items-center gap-x-6">
                    <a href="./privacy.html" class="underline hover:text-white transition-colors">Privacy Policy</a>
                    <a href="https://discord.gg/PxjD25Cc85" class="underline hover:text-white transition-colors">Support Server</a>
                    <a href="https://github.com/Vulps22/project-gamer" target="_blank"
                        class="hover:text-white transition-colors text-lg">
                        <i class="fa-brands fa-github"></i>
                    </a>
                </div>
            </div>

            <div class="hidden md:block md:col-span-3"></div>
        </div>
    </footer>
</body>

</html>
