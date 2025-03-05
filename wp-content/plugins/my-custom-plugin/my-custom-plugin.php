<?php
/*
Plugin Name: Membership Plugin
Description: A simple Membership 
Version:     1.0
Author:      sanny

*/


function wizard_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'wizard_start_session');


function wizard_enqueue_scripts() {
    wp_enqueue_style( 'tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css' );
    wp_enqueue_script('wizard-script', plugin_dir_url(__FILE__) . 'wizard-script.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'wizard_enqueue_scripts');


function wizard_handle_zipcode_form() {
    $zipcodeData = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zipcode'])) {
        $zipcode = trim($_POST['zipcode']);
        
        if (!preg_match('/^\d{5}$/', $zipcode)) {
            $error = "Please enter a valid 5-digit ZIP code.";
        } else {
            $url = "http://api.zippopotam.us/us/$zipcode";
            $response = @file_get_contents($url);
            
            if ($response !== false) {
                $zipcodeData = json_decode($response, true);
                $_SESSION['zipcode'] = $zipcode;
                wp_redirect('?step=choose');
                exit;
            } else {
                $_SESSION['error'] = "Invalid ZIP code. Please try again.";
            }
        }
    }

    return compact('zipcodeData');

    
}


function wizard_handle_choose_form() {
    $error = '';
    $zipcode = isset($_SESSION['zipcode']) ? $_SESSION['zipcode'] : ''; 
    $household = '';  

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['household']) && isset($_SESSION['zipcode'])) {
        $household = $_GET['household'];  
        $_SESSION['household'] = $household; 
        $_SESSION['zipcode'] = $zipcode;  

     
        wp_redirect('?step=next');  
        exit;
    }

    return compact('error', 'household');
}


function wizard_handle_next_form() {
    $error = '';
    $dinners = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dinners'])) {
        $dinners = $_POST['dinners'];
        $_SESSION['dinners'] = $dinners;
        wp_redirect('?step=bundle');
        exit;
    }
    return compact('error', 'dinners');


}


function wizard_handle_bundle_form() {
    $error = '';
    $bundles = $_SESSION['bundles'] ?? [];
    $totalPrice = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bundles'])) {
        $selectedBundles = $_POST['bundles'] ?? [];
        $prices = [
            'breakfast' => 20,
            'lunch' => 30,
            'kids' => 20,
            'pressed_juice' => 24
        ];

        foreach ($selectedBundles as $bundle) {
            if (isset($prices[$bundle])) {
                $totalPrice += $prices[$bundle];
            }
        }


        $_SESSION['bundles'] = $selectedBundles;
        $_SESSION['totalPrice'] = $totalPrice;
        wp_redirect('?step=summary');
        exit;
    }
    return compact('error', 'bundles', 'totalPrice');
}


function wizard_shortcode() {
    $step = isset($_GET['step']) ? $_GET['step'] : 'zip';
    $form_data = wizard_handle_zipcode_form();
    $form_data = wizard_handle_choose_form();
    $form_data = wizard_handle_next_form();
    $form_data = wizard_handle_bundle_form();
    ob_start();
    ?>

    <div class="wizard-container bg-gray-100 min-h-screen flex flex-col justify-center items-center">
        <div class="m-6">
            <h1 class="text-4xl font-semibold text-center text-gray-800">Build Your Personalized Plan</h1>
        </div>

        <?php if ($step === 'zip') : ?>
            <div class="max-w-lg w-full bg-white p-8 rounded-lg shadow-lg">
                <!-- Form for ZIP Code -->
                <form method="POST" class="space-y-4">
                    <div>
                        <input type="text" id="zipcode" name="zipcode" placeholder="Enter ZIP Code" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?php echo isset($zipcode) ? htmlspecialchars($zipcode) : ''; ?>" required>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
    <div class="text-red-500 text-sm"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']);  ?>
<?php endif; ?>


                    <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Check</button>
                </form>

                <p class="text-center text-gray-500 mt-4">Let's confirm that we can deliver to your area</p>
            </div>
            
    <!-- Household Selection -->
        <?php elseif ($step === 'choose') : ?>
        
            <div class="bg-white shadow-lg rounded-lg mx-auto mt-20 p-8">
    <div class="text-center">
        <p class="text-gray-600 mt-2">Great news! We deliver to your area!</p>
    </div>

    <div class="mt-10 text-center">
        <h2 class="text-2xl font-semibold text-gray-800">How big is your household?</h2>
    </div>

    <!-- Household Options -->
    <div class="flex justify-center space-x-8 mt-6">
        <!-- Singles Option -->
        <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-household="1" onclick="selectOption(this)">
            <div class="bg-gray-100 p-4 rounded-full mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 64 32" class="h-6 sm:h-5" role="presentation"><path fill="#2C2C2C" d="M38.342 20.94a7 7 0 0 0-.157-.695c-.034-.123-.067-.245-.112-.367a6 6 0 0 0-.46-.973 5 5 0 0 0-.297-.45.7.7 0 0 0-.079-.095 6 6 0 0 0-.628-.712 6.3 6.3 0 0 0-.846-.684 6 6 0 0 0-.668-.383 6.7 6.7 0 0 0-1.934-.645 7 7 0 0 0-1.155-.1c-2.187 0-4.15 1.028-5.31 2.624a5.66 5.66 0 0 0-1.027 2.485l-.65 4.22q-.025.183-.017.356c.04.584.404 1.09.92 1.329.207.094.437.15.684.15h10.783c.291 0 .555-.083.785-.211.067-.04.129-.073.19-.117.18-.134.331-.306.438-.506.163-.295.235-.645.18-1.006l-.651-4.22zM29.746 13.328a4.04 4.04 0 0 0 2.26.684 4.1 4.1 0 0 0 2.573-.912c.191-.156.36-.328.516-.511a3.784 3.784 0 0 0 .634-1.018c.202-.478.32-1.006.32-1.562s-.113-1.079-.32-1.563c-.101-.239-.23-.467-.37-.678a4.46 4.46 0 0 0-.78-.85 4 4 0 0 0-.745-.479 4 4 0 0 0-1.829-.433 4.04 4.04 0 0 0-2.86 1.173 3.988 3.988 0 0 0 .6 6.16z"></path></svg>
            </div>
            <p class="text-2xl font-bold text-gray-800">SINGLES</p>
<p class="text-base text-gray-600 mt-1">Dinners prepared for 1</p>

        </div>

        <!-- Couples Option -->
        <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-household="2" onclick="selectOption(this)">
            <div class="bg-gray-100 p-4 rounded-full mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 64 32" class="h-6 sm:h-5" role="presentation"><path fill="#2C2C2C" d="M46.342 20.94a7 7 0 0 0-.157-.695c-.034-.123-.067-.245-.112-.367a6 6 0 0 0-.46-.973 5 5 0 0 0-.297-.45.7.7 0 0 0-.079-.095 6 6 0 0 0-.628-.712 6.3 6.3 0 0 0-.846-.684 6 6 0 0 0-.668-.383 6.7 6.7 0 0 0-1.934-.645 7 7 0 0 0-1.155-.1c-2.187 0-4.15 1.028-5.31 2.624a5.66 5.66 0 0 0-1.027 2.485l-.65 4.22q-.026.183-.017.356c.04.584.404 1.09.92 1.329.207.094.437.15.684.15h10.783c.291 0 .555-.083.785-.211.067-.04.129-.073.19-.117.18-.134.331-.306.438-.506.163-.295.235-.645.18-1.006l-.651-4.22zM37.746 13.328a4.04 4.04 0 0 0 2.26.684 4.1 4.1 0 0 0 2.573-.912c.191-.156.36-.328.516-.511a3.784 3.784 0 0 0 .634-1.018c.202-.478.32-1.006.32-1.562s-.113-1.079-.32-1.563c-.101-.239-.23-.467-.37-.678a4.46 4.46 0 0 0-.78-.85 4 4 0 0 0-.745-.479 4 4 0 0 0-1.829-.433 4.04 4.04 0 0 0-2.86 1.173 3.988 3.988 0 0 0 .6 6.16zM30.342 20.94a7 7 0 0 0-.157-.695c-.034-.123-.067-.245-.112-.367a6 6 0 0 0-.46-.973 5 5 0 0 0-.297-.45.7.7 0 0 0-.079-.095 6 6 0 0 0-.628-.712 6.3 6.3 0 0 0-.846-.684 6 6 0 0 0-.668-.383 6.7 6.7 0 0 0-1.934-.645 7 7 0 0 0-1.155-.1c-2.187 0-4.15 1.028-5.31 2.624a5.66 5.66 0 0 0-1.027 2.485l-.65 4.22q-.025.183-.017.356c.04.584.404 1.09.92 1.329.207.094.437.15.684.15h10.783c.291 0 .555-.083.785-.211.067-.04.129-.073.19-.117.18-.134.331-.306.438-.506.163-.295.235-.645.18-1.006l-.651-4.22zM21.746 13.328a4.04 4.04 0 0 0 2.26.684 4.1 4.1 0 0 0 2.573-.912c.191-.156.36-.328.516-.511a3.784 3.784 0 0 0 .634-1.018c.202-.478.32-1.006.32-1.562s-.113-1.079-.32-1.563c-.101-.239-.23-.467-.37-.678a4.46 4.46 0 0 0-.78-.85 4 4 0 0 0-.745-.479 4 4 0 0 0-1.828-.433 4.04 4.04 0 0 0-2.86 1.173 3.988 3.988 0 0 0 .6 6.16z"></path></svg>
            </div>
            <p class="text-2xl font-bold text-gray-800"> COUPLE</p>
<p class="text-base text-gray-600 mt-1">Dinners prepared for 2</p>

        </div>

        <!-- Family Option -->
        <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-household="4" onclick="selectOption(this)">
            <div class="bg-gray-100 p-4 rounded-full mb-4">
               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 64 32" class="h-6 sm:h-5" role="presentation"><path fill="#2C2C2C" d="M50.342 20.94a7 7 0 0 0-.157-.695c-.034-.123-.067-.245-.112-.367a6 6 0 0 0-.46-.973 5 5 0 0 0-.297-.45.7.7 0 0 0-.079-.095 6 6 0 0 0-.628-.712 6.3 6.3 0 0 0-.846-.684 6 6 0 0 0-.668-.383 6.7 6.7 0 0 0-1.934-.645 7 7 0 0 0-1.155-.1c-2.187 0-4.15 1.028-5.31 2.624a5.66 5.66 0 0 0-1.027 2.485l-.65 4.22q-.026.183-.017.356c.04.584.404 1.09.92 1.329.207.094.437.15.684.15h10.783c.291 0 .555-.083.785-.211.067-.04.129-.073.19-.117.18-.134.331-.306.438-.506.163-.295.235-.645.18-1.006l-.651-4.22zM41.746 13.328a4.04 4.04 0 0 0 2.26.684 4.1 4.1 0 0 0 2.573-.912c.191-.156.36-.328.516-.511a3.784 3.784 0 0 0 .634-1.018c.202-.478.32-1.006.32-1.562s-.113-1.079-.32-1.563c-.101-.239-.23-.467-.37-.678a4.46 4.46 0 0 0-.78-.85 4 4 0 0 0-.745-.479 4 4 0 0 0-1.829-.433 4.04 4.04 0 0 0-2.86 1.173 3.988 3.988 0 0 0 .6 6.16zM34.342 20.94a7 7 0 0 0-.157-.695c-.034-.123-.067-.245-.112-.367a6 6 0 0 0-.46-.973 5 5 0 0 0-.297-.45.7.7 0 0 0-.079-.095 6 6 0 0 0-.628-.712 6.3 6.3 0 0 0-.846-.684 6 6 0 0 0-.668-.383 6.7 6.7 0 0 0-1.934-.645 7 7 0 0 0-1.155-.1c-2.187 0-4.15 1.028-5.31 2.624a5.66 5.66 0 0 0-1.027 2.485l-.65 4.22q-.025.183-.017.356c.04.584.404 1.09.92 1.329.207.094.437.15.684.15h10.783c.291 0 .555-.083.785-.211.067-.04.129-.073.19-.117.18-.134.331-.306.438-.506.163-.295.235-.645.18-1.006l-.651-4.22zM25.746 13.328a4.04 4.04 0 0 0 2.26.684 4.1 4.1 0 0 0 2.573-.912c.191-.156.36-.328.516-.511a3.784 3.784 0 0 0 .634-1.018c.202-.478.32-1.006.32-1.562s-.113-1.079-.32-1.563c-.101-.239-.23-.467-.37-.678a4.46 4.46 0 0 0-.78-.85 4 4 0 0 0-.745-.479 4 4 0 0 0-1.828-.433 4.04 4.04 0 0 0-2.86 1.173 3.988 3.988 0 0 0 .6 6.16zM18.342 20.94a7 7 0 0 0-.157-.695c-.034-.123-.067-.245-.112-.367a6 6 0 0 0-.46-.973 5 5 0 0 0-.297-.45.7.7 0 0 0-.079-.095 6 6 0 0 0-.628-.712 6.3 6.3 0 0 0-.846-.684 6 6 0 0 0-.668-.383 6.7 6.7 0 0 0-1.934-.645 7 7 0 0 0-1.155-.1c-2.187 0-4.15 1.028-5.31 2.624a5.66 5.66 0 0 0-1.027 2.485l-.65 4.22q-.025.183-.017.356c.04.584.404 1.09.92 1.329.207.094.437.15.684.15h10.783c.291 0 .555-.083.785-.211.067-.04.129-.073.19-.117.18-.134.331-.306.438-.506.163-.295.235-.645.18-1.006l-.651-4.22zM9.746 13.328a4.04 4.04 0 0 0 2.26.684 4.1 4.1 0 0 0 2.573-.912c.191-.156.36-.328.516-.511a3.784 3.784 0 0 0 .634-1.018c.202-.478.32-1.006.32-1.562s-.113-1.079-.32-1.563c-.101-.239-.23-.467-.37-.678a4.46 4.46 0 0 0-.78-.85 4 4 0 0 0-.745-.479 4 4 0 0 0-1.828-.433 4.04 4.04 0 0 0-2.86 1.173 3.988 3.988 0 0 0 .6 6.16zM57 17v2h-2v-2h-2v-2h2v-2h2v2h2v2z"></path></svg>
            </div>
            <p class="text-2xl font-bold text-gray-800">FAMILY</p>
<p class="text-base text-gray-600 mt-1">Dinners prepared for 4</p>

        </div>
    </div>

    <div class="mt-8 text-center flex justify-center">
        <form method="GET">
            <input type="hidden" id="selected-household" name="household" value="">
            <input type="hidden" name="zipcode" value="<?php echo $_SESSION['zipcode']; ?>"> 
            <button id="next-button" type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition" style="display:none;">Next</button>
        </form>
    </div>

    <!-- Navigation -->
    <div class="mt-8 text-center">
        <h2>
        <a href="index.php"  class="text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors duration-300 ease-in-out">Back to Zip Code Entry</a>
        </h2>
    </div>
</div>


  <!-- Dinners Selection Section -->
 <?php elseif ($step === 'next') : ?>
    <div class="bg-white shadow-lg rounded-lg mx-auto mt-20 p-8">

    <div class="text-center">
        <p class="text-gray-600 mt-2">Choose the number of dinners per week.</p>
    </div>

  
    <div class="mt-10 text-center">
        <h2 class="text-2xl font-semibold text-gray-800">How many dinners per week?</h2>
    </div>

    <!-- Dinner Options -->
    <div class="flex justify-center space-x-6 mt-8">
    <!-- 6 Dinners Option -->
    <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-dinners="6" onclick="selectOption(this)">
        <p class="text-6xl font-extrabold text-gray-800">6</p>
        <p class="text-sm text-gray-600 uppercase">Dinners per week</p>
        <p id="discount-6" class="text-sm text-green-600 mt-2"></p>
    </div>

    <!-- 5 Dinners Option -->
    <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-dinners="5" onclick="selectOption(this)">
        <p class="text-6xl font-extrabold text-gray-800">5</p>
        <p class="text-sm text-gray-600 uppercase">Dinners per week</p>
        <p id="discount-5" class="text-sm text-green-600 mt-2"></p>
    </div>

    <!-- 4 Dinners Option -->
    <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-dinners="4" onclick="selectOption(this)">
        <p class="text-6xl font-extrabold text-gray-800">4</p>
        <p class="text-sm text-gray-600 uppercase">Dinners per week</p>
        <p id="discount-4" class="text-sm text-green-600 mt-2"></p>
    </div>

    <!-- 3 Dinners Option -->
    <div class="option flex flex-col items-center bg-white p-8 rounded-lg shadow-xl cursor-pointer hover:bg-gray-50 border border-transparent transition-transform transform hover:scale-105" data-dinners="3" onclick="selectOption(this)">
        <p class="text-6xl font-extrabold text-gray-800">3</p>
        <p class="text-sm text-gray-600 uppercase">Dinners per week</p>
        <p id="discount-3" class="text-sm text-green-600 mt-2"></p>
    </div>
</div>


    <!-- Next Button -->
    <div class="mt-8 text-center flex justify-center">
        <form method="POST" action="?step=bundle">
            <input type="hidden" id="selected-dinners" name="dinners" value="">
            <button id="next-button" type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition" style="display:none;">Next</button>
        </form>
    </div>

 <!-- Navigation -->
<div class="mt-8 text-center">
    <h2>
        <a href="?step=choose" class="text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors duration-300 ease-in-out">
            Back to Household Selection
        </a>
    </h2>
</div>

</div>


        <!-- Bundle Selection -->
<?php elseif ($step === 'bundle') : ?>

    <div class="bg-white shadow-lg rounded-lg mx-auto mt-20 p-8">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-gray-800">Add Bundles to Your Plan</h2>
            <p class="text-gray-600 mt-2">Pick three extra items from our curated sections each week.</p>
        </div>


        <form  method="POST" class="mt-10">
            <div class="flex flex-wrap justify-center space-x-8 mt-6">
        <!-- Breakfast -->
        <div class="bundle-option w-sm flex flex-col items-center bg-white p-6 rounded-lg shadow-lg cursor-pointer hover:bg-gray-50 border border-transparent transition duration-300 ease-in-out transform hover:scale-105">
                <input type="checkbox" name="bundles[]" value="breakfast" class="mb-2 w-6 h-6" />
                <p class="text-xl font-semibold text-gray-800">BREAKFAST</p>
                <p class="text-sm text-gray-500">3 ITEMS</p>
                <p class="text-sm text-gray-500">$20</p>
            </div>

            <!-- Lunch -->
            <div class="bundle-option w-sm flex flex-col items-center bg-white p-6 rounded-lg shadow-lg cursor-pointer hover:bg-gray-50 border border-transparent transition duration-300 ease-in-out transform hover:scale-105">
                <input type="checkbox" name="bundles[]" value="lunch" class="mb-2 w-6 h-6" />
                <p class="text-xl font-semibold text-gray-800">LUNCH</p>
                <p class="text-sm text-gray-500">3 ITEMS</p>
                <p class="text-sm text-gray-500">$30</p>
            </div>

            <!-- Kids -->
            <div class="bundle-option w-sm flex flex-col items-center bg-white p-6 rounded-lg shadow-lg cursor-pointer hover:bg-gray-50 border border-transparent transition duration-300 ease-in-out transform hover:scale-105">
                <input type="checkbox" name="bundles[]" value="kids" class="mb-2 w-6 h-6" />
                <p class="text-xl font-semibold text-gray-800">KIDS</p>
                <p class="text-sm text-gray-500">3 ITEMS</p>
                <p class="text-sm text-gray-500">$20</p>
            </div>

            <!-- Pressed Juice -->
            <div class="bundle-option w-sm flex flex-col items-center bg-white p-6 rounded-lg shadow-lg cursor-pointer hover:bg-gray-50 border border-transparent transition duration-300 ease-in-out transform hover:scale-105">
                <input type="checkbox" name="bundles[]" value="pressed_juice" class="mb-2 w-6 h-6" />
                <p class="text-xl font-semibold text-gray-800">PRESSED JUICE</p>
                <p class="text-sm text-gray-500">3 ITEMS</p>
                <p class="text-sm text-gray-500">$24</p>
            </div>
        </div>


            <!-- Submit Button -->
            <div class="mt-8 text-center">
            <button id="next-button" type="submit" class="bg-orange-500 text-white p-3 rounded-lg hover:bg-orange-600 transition" style="background-color: #F97316 !important;">Next</button>
            </div>
        </form>
    </div>

        <!-- Summary Section -->
<?php elseif ($step === 'summary') : ?>
    <div class="bg-white shadow-lg rounded-lg mx-auto mt-20 p-8 max-w-xl">
    <div class="text-center">
        <h2 class="text-2xl font-semibold text-gray-800">Summary</h2>
        <p class="text-gray-600 mt-2">Review your selections before placing your order.</p>
    </div>


    <div class="mt-8">
        <div class="text-lg font-semibold text-gray-800 mb-2">Household</div>
        <div class="bg-gray-50 border rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">Household Size: <span class="font-medium text-gray-800"><?php echo $_SESSION['household']; ?></span></p>
        </div>

        <div class="text-lg font-semibold text-gray-800 mb-2">Dinners Per Week</div>
        <div class="bg-gray-50 border rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">Meals Delivered: <span class="font-medium text-gray-800"><?php echo $_SESSION['dinners']; ?></span></p>
        </div>

        <div class="text-lg font-semibold text-gray-800 mb-2">Selected Bundles</div>
        <div class="bg-gray-50 border rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">Bundles: <span class="font-medium text-gray-800"><?php echo implode(', ', $_SESSION['bundles']); ?></span></p>
        </div>


        <?php 
            $basePrice = $_SESSION['totalPrice'];  
            $dinnersPerWeek = $_SESSION['dinners']; 
            $totalPrice = $basePrice * $dinnersPerWeek; 
        ?>

        <div class="text-lg font-semibold text-gray-800 mb-2">Total Price</div>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6">
            <p class="text-xl font-semibold text-gray-800">Total: <span class="text-2xl font-bold text-yellow-600">$<?php echo number_format($totalPrice, 2); ?></span></p>
        </div>
    </div>

    <!-- Navigation -->
    <div class="mt-8 text-center">
        <h2>
        <a href="?step=zip" class="text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors duration-300 ease-in-out">Start Over</a>
        </h2>
       
    </div>
</div>






<?php endif; ?>
    </div>



    <?php
    return ob_get_clean();
}
add_shortcode('member_sanny', 'wizard_shortcode');
