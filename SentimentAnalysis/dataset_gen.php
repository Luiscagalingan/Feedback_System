<?php

// Your labeled dataset
$labeledDataset = [
    ['text' => 'I love this product', 'label' => 'positive'],
    ['text' => 'This movie is terrible', 'label' => 'negative'],
    ['text' => 'panget nang website', 'label' => 'negative'],
    ['text' => 'di siya user friendly', 'label' => 'negative'],
    ['text' => 'Maganda yung website', 'label' => 'positive'],
    ['text' => 'This is a really helpful website', 'label' => 'positive'],
    ['text' => 'The system is easy to use', 'label' => 'positive'],
    ['text' => 'Showing great development', 'label' => 'positive'],
    ['text' => 'Maayos ang system', 'label' => 'positive'],
    ['text' => 'Madali maaccess', 'label' => 'positive'],
        ['text' => 'I love this product', 'label' => 'positive'],
    ['text' => 'This movie is terrible', 'label' => 'negative'],
    ['text' => 'panget nang website', 'label' => 'negative'],
    ['text' => 'di siya user friendly', 'label' => 'negative'],
    ['text' => 'Maganda yung website', 'label' => 'positive'],
    ['text' => 'This is a really helpful website', 'label' => 'positive'],
    ['text' => 'The system is easy to use', 'label' => 'positive'],
    ['text' => 'Showing great development', 'label' => 'positive'],
    ['text' => 'Maayos ang system', 'label' => 'positive'],
    ['text' => 'Madali maaccess', 'label' => 'positive'],
    ['text' => 'This system is easy to use and it will really help the association to track its alumni.', 'label' => 'positive'],
    ['text' => 'This was a nice website', 'label' => 'positive'],
    ['text' => 'maayos naman', 'label' => 'neutral'],
    ['text' => 'pwede na', 'label' => 'neutral'],
    ['text' => 'okay na', 'label' => 'neutral'],
    ['text' => 'Mabagal magload', 'label' => 'negative'],
    ['text' => 'Ang tagal magsend ng otp', 'label' => 'negative'],
    ['text' => 'hindi siya maganda', 'label' => 'negative'],
    ['text' => 'Terrible experience! The product didn\'t work as advertised, and customer support was unhelpful.', 'label' => 'negative'],
    ['text' => 'Waste of money. The quality is poor, and it broke after just a few uses.', 'label' => 'negative'],
    ['text' => 'I wouldn\'t recommend this service to anyone. It\'s slow, unreliable, and full of glitches.', 'label' => 'negative'],
    ['text' => 'The worst customer service ever. They were rude and unresponsive to my issues.', 'label' => 'negative'],
    ['text' => 'I\'m extremely disappointed. The software is filled with bugs, and updates only make things worse.', 'label' => 'negative'],
    ['text' => 'Impressive system! It\'s user-friendly and efficiently streamlines tasks, making my work much easier.', 'label' => 'positive'],
    ['text' => 'Love the features! This system offers a wide range of tools that enhance productivity and performance.', 'label' => 'positive'],
    ['text' => 'Outstanding customer support! Whenever I had an issue, the support team was quick to respond and resolve it.', 'label' => 'positive'],
    ['text' => 'A game-changer! This system has significantly improved our workflow and overall efficiency.', 'label' => 'positive'],
    ['text' => 'Reliable and secure. I trust this system to handle sensitive information, and it has never let me down.', 'label' => 'positive'],
    ['text' => 'Intuitive design! The interface is easy to navigate, even for those with minimal technical expertise.', 'label' => 'positive'],
    ['text' => 'Fast and efficient! Tasks that used to take hours now get done in a fraction of the time with this system.', 'label' => 'positive'],
    ['text' => 'Great value for money. The benefits and features provided by this system far exceed its cost', 'label' => 'positive'],
    ['text' => 'Regular updates with useful improvements. It\'s evident that the developers are committed to enhancing the user experience.', 'label' => 'positive'],
    ['text' => 'Seamless integration with other tools. This system easily integrates with our existing software, making it a valuable addition to our tech stack.', 'label' => 'positive'],
    ['text' => 'The system functions adequately, meeting the basic requirements without any standout features', 'label' => 'neutral'],
    ['text' => 'It\'s a standard system. Not particularly impressive, but it gets the job done without major issues.', 'label' => 'neutral'],
    ['text' => 'Average performance. The system is neither exceptional nor subpar in its functionality.', 'label' => 'neutral'],
    ['text' => 'Neutral experience. No major complaints, but I haven\'t found any remarkable benefits either', 'label' => 'neutral'],
    ['text' => 'The system is okay. It hasn\'t exceeded expectations, but it hasn\'t disappointed either.', 'label' => 'neutral'],
    ['text' => 'It\'s a middle-of-the-road option. I haven\'t encountered significant problems, but it lacks any wow factor', 'label' => 'neutral'],
    ['text' => 'Decent functionality. It does what it\'s supposed to do, but there\'s nothing remarkable about it.', 'label' => 'neutral'],
    ['text' => 'Satisfactory performance. The system meets the basic requirements without excelling in any particular area.', 'label' => 'neutral'],
    ['text' => 'No strong opinions either way. It\'s a standard system that fulfills its purpose without standing out', 'label' => 'neutral'],
    ['text' => 'I have a neutral stance on this system. It\'s neither exceptional nor terrible, just an average tool for the job.', 'label' => 'neutral'],
    ['text' => 'Maganda ang sistema! Napakadali gamitin at sobrang epektibo para sa trabaho ko.', 'label' => 'positive'],
    ['text' => 'Ang dami ng features! Ang sistema ay nagbibigay ng maraming tool na nagpapabuti sa aking produksyon at performance.', 'label' => 'positive'],
    ['text' => 'Napakahusay ng customer support! Tuwing may problema ako, mabilis ang tugon ng support team at maayos agad ang issue', 'label' => 'positive'],
    ['text' => 'Isang malaking ginhawa! Ang sistema ay lubos na nagpapabuti sa aming takbo ng trabaho at kabuuang epektibidad.', 'label' => 'positive'],
    ['text' => 'Mapagkakatiwalaan at ligtas. Tiwala ako na kayang-kaya nitong pangalagaan ang mga sensitibong impormasyon, at hindi pa ako binibigo', 'label' => 'positive'],
    ['text' => 'Intuitive ang design! Madali ang interface na gamitin, kahit para sa mga hindi gaanong may kakayahan sa teknolohiya.', 'label' => 'positive'],
    ['text' => 'Mabilis at maaasahan! Ang mga gawain na dati\'y tumatagal ng oras, ngayon ay natatapos ng mas mabilis gamit ang sistema na ito', 'label' => 'positive'],
    ['text' => 'Magandang halaga para sa pera. Ang mga benepisyo at features na ibinibigay ng sistema ay higit pa sa halaga nito', 'label' => 'positive'],
    ['text' => 'Regular ang mga updates na may kapaki-pakinabang na improvements. Kitang-kita na committed ang mga developers sa pagpapabuti ng karanasan ng mga gumagamit.', 'label' => 'positive'],
    ['text' => 'Maganda ang integrasyon sa ibang tools. Madali nitong ma-integrate sa aming kasalukuyang software, kaya\'t isang mahalagang dagdag sa aming tech stack.', 'label' => 'positive'],
    ['text' => 'Nakakalungkot na karanasan! Ang sistema ay puno ng mga bugs na nagbabawas sa basic na kahusayan nito', 'label' => 'negative'],
    ['text' => 'Masamang customer support. Matagal na akong naghihintay ng sagot sa aking problema, pero wala pa ring solusyon.', 'label' => 'negative'],
    ['text' => 'Hindi user-friendly. Magulo ang interface, at masyadong matagal ang pag-aaral para sa mga basic na gawain.', 'label' => 'negative'],
    ['text' => 'Palaging nag-crash. Ang sistema ay hindi stable; mahirap magtrabaho nang walang abala', 'label' => 'negative'],
    ['text' => 'Mahal para sa iniaalok. Inaasahan ko ang mas maraming features at mas magandang performance para sa presyo nito', 'label' => 'negative'],
    ['text' => 'Luma na ang teknolohiya. Parang gamit ko ang isang sistema mula sa isang dekada na ang nakalipas na walang modernong pagbabago.', 'label' => 'negative'],
    ['text' => 'May mga pangamba sa seguridad. Hindi ko itinatangi ang sistema na ito ng mga sensitibong datos; maraming kahinaan.', 'label' => 'negative'],
    ['text' => 'Kulang sa mahahalagang features. Wala ang basic na mga kakayahan, kaya\'t nakakainis gamitin', 'label' => 'negative'],
    ['text' => 'Mabagal ang mga updates. Hindi mabilis ang pag-angkop ng sistema sa nagbabagong pangangailangan, at nahuhuli sa kumpetisyon', 'label' => 'negative'],
    ['text' => 'Masamang integrasyon. Hindi maganda ang pagtugma nito sa ibang tools, na nagdudulot ng mga problema sa pagiging compatible at workflow.', 'label' => 'negative'],
    ['text' => 'Ang sistema ay gumagana ng maayos, nakakatugon sa mga pangunahing pangangailangan nang walang mga kahanga-hangang feature', 'label' => 'neutral'],
    ['text' => 'Isang standard na sistema. Hindi espesyal, ngunit nagagampanan nang walang malalang isyu ang kanyang tungkulin.', 'label' => 'neutral'],
    ['text' => 'Hindi masyadong kakaiba ang performance. Ang sistema ay hindi kahanga-hanga ngunit hindi rin naman napakababa', 'label' => 'neutral'],
    ['text' => 'Neutral na karanasan. Walang malalang reklamo, ngunit wala rin namang kahanga-hangang benepisyo na natagpuan', 'label' => 'neutral'],
    ['text' => 'Ang sistema ay okay. Hindi ito lumampas sa mga asa, ngunit hindi rin ito bumagsak.', 'label' => 'neutral'],
    ['text' => 'Katamtaman ang performance. Ang sistema ay nagtutupad ng kanyang layunin nang hindi umaatras o umaangat', 'label' => 'neutral'],
    ['text' => 'Wala itong espesyal na aspeto. Ang sistema ay nagbibigay ng pangunahing kakayahan ngunit wala itong nakikitang kakaibang aspeto.', 'label' => 'neutral'],
    ['text' => 'Katanggap-tanggap ang performance. Ang sistema ay nagtutugma sa pangunahing pangangailangan ngunit hindi ito kakaiba.', 'label' => 'neutral'],
    ['text' => 'Walang malalim na opinyon. Ito ay isang karaniwang sistema na nakakatugon sa pangunahing pangangailangan', 'label' => 'neutral'],
    ['text' => 'Neutral ang aking posisyon sa sistema. Hindi ito espesyal ngunit hindi rin ito masama, isang karaniwang kasangkapan para sa gawain.', 'label' => 'neutral'],
    ['text' => 'Panget', 'label' => 'negative'],
    ['text' => 'maganda', 'label' => 'positive'],
    ['text' => 'maayos', 'label' => 'positive'],
    ['text' => 'hindi maganda', 'label' => 'negative'],
    ['text' => 'sarap', 'label' => 'positive'],
    ['text' => 'asim', 'label' => 'negative'],
    ['text' => 'tangina', 'label' => 'negative'],
    ['text' => 'ganda', 'label' => 'positive'],
    ['text' => 'loko', 'label' => 'negative'],
    ['text' => 'ayos', 'label' => 'positive'],
    ['text' => 'pede na', 'label' => 'neutral'],
    ['text' => 'Maayos naman', 'label' => 'neutral'],
    // Add more labeled data as needed
];

// Initialize lexicon
$lexicon = [];

// Process each labeled text
foreach ($labeledDataset as $data) {
    $text = strtolower($data['text']); // lowercase
    $label = $data['label'];

    // Assign a numeric score
    $score = 0;
    if ($label === 'positive') {
        $score = 2;
    } elseif ($label === 'negative') {
        $score = -2;
    } elseif ($label === 'neutral') {
        $score = 0;
    }

    // Split text into words
    $words = preg_split('/\s+/', $text);

    foreach ($words as $word) {
        // Remove punctuation
        $word = trim($word, ".,!?;:'\"()[]{}");
        if ($word === '') continue;

        // Aggregate scores
        if (!isset($lexicon[$word])) {
            $lexicon[$word] = 0;
        }
        $lexicon[$word] += $score;
    }
}

// Optional: Normalize scores to range -1 to 1
$maxAbs = max(array_map('abs', $lexicon));
if ($maxAbs > 0) {
    foreach ($lexicon as $word => $value) {
        $lexicon[$word] = round($value / $maxAbs, 2);
    }
}

// Save lexicon to JSON
file_put_contents('words1.json', json_encode($lexicon, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Lexicon generated successfully! Check 'words.json'.\n";
?>
