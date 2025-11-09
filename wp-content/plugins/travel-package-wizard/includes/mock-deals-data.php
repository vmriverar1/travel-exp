<?php
/**
 * Mock Deals Data
 * 10 promotional deals (mix of active and inactive)
 */

if (!defined('ABSPATH')) {
    exit;
}

return [
    // 1. Active Deal - Early Bird Special
    [
        'title' => 'Early Bird Special - Save 20% on 2025 Inca Trail',
        'active' => true,
        'start_date' => '2024-11-01 00:00:00',
        'end_date' => '2025-02-28 23:59:59',
        'discount_percentage' => 20,
        'description' => '<p>Book your 2025 Inca Trail adventure now and save 20%! This early bird special is perfect for travelers who plan ahead and want to secure their spot on Peru\'s most iconic trek.</p>
<p><strong>Why Book Early?</strong></p>
<ul>
<li>Save 20% on regular prices</li>
<li>Guaranteed departure dates</li>
<li>Best campsite locations</li>
<li>First choice of trekking dates</li>
<li>Flexible payment plans available</li>
</ul>
<p>The Inca Trail permits sell out months in advance. Don\'t miss your chance to experience this bucket-list adventure at our best price of the year!</p>',
        'terms' => 'Valid for bookings made between November 1, 2024 and February 28, 2025. Travel must be completed by December 31, 2025. Non-refundable deposit required. 20% discount applies to trek price only, excludes permits and equipment rental. Subject to availability. Cannot be combined with other offers.',
    ],

    // 2. Active Deal - Summer Adventure Package
    [
        'title' => 'Summer Adventure - 15% Off Multi-Day Treks',
        'active' => true,
        'start_date' => '2024-12-01 00:00:00',
        'end_date' => '2025-03-31 23:59:59',
        'discount_percentage' => 15,
        'description' => '<p>Experience Peru\'s stunning mountain scenery this summer with 15% off all multi-day trekking adventures! Choose from Salkantay, Lares, Ausangate, or create your own custom itinerary.</p>
<p><strong>Featured Treks:</strong></p>
<ul>
<li>5-Day Salkantay Trek to Machu Picchu</li>
<li>4-Day Lares Cultural Trek</li>
<li>7-Day Ausangate Circuit</li>
<li>6-Day Choquequirao Adventure</li>
</ul>
<p>All treks include professional guides, quality camping equipment, delicious meals, and porters. Small group sizes ensure personalized attention.</p>',
        'terms' => 'Valid December 1, 2024 - March 31, 2025. Minimum 4-day trek required. Discount applies to base trek price. Does not include Machu Picchu entrance tickets or train tickets. 30-day advance booking required. Subject to weather conditions and guide availability.',
    ],

    // 3. Active Deal - Family Vacation Special
    [
        'title' => 'Family Vacation Special - Kids Travel Free',
        'active' => true,
        'start_date' => '2024-12-15 00:00:00',
        'end_date' => '2025-04-15 23:59:59',
        'discount_percentage' => 30,
        'description' => '<p>Bring the whole family to Peru! Kids under 12 travel FREE when accompanied by two paying adults on our specially designed family tours.</p>
<p><strong>Family-Friendly Features:</strong></p>
<ul>
<li>Engaging activities for all ages</li>
<li>Flexible itineraries</li>
<li>Kid-approved meals</li>
<li>Expert family guides</li>
<li>Educational experiences</li>
<li>Safe, comfortable accommodations</li>
</ul>
<p>Our family specialist Angela creates unforgettable experiences that educate and entertain children while giving parents peace of mind.</p>',
        'terms' => 'Kids 12 and under travel free (up to 2 children per 2 paying adults). Valid for family tour packages only. Blackout dates: December 20-31, 2024. Minimum 5-day tour required. Children must be accompanied by adults at all times. Age verification required. International airfare not included.',
    ],

    // 4. Active Deal - Couples Getaway
    [
        'title' => 'Romantic Getaway - Honeymoon & Anniversary Special',
        'active' => true,
        'start_date' => '2025-01-01 00:00:00',
        'end_date' => '2025-06-30 23:59:59',
        'discount_percentage' => 25,
        'description' => '<p>Celebrate love in Peru! Our romantic packages include private tours, luxury accommodations, and special touches to make your honeymoon or anniversary unforgettable.</p>
<p><strong>Romantic Inclusions:</strong></p>
<ul>
<li>Champagne and flowers on arrival</li>
<li>Couples massage session</li>
<li>Private candlelit dinner</li>
<li>Sunrise hot air balloon ride (Sacred Valley)</li>
<li>Private tour guides</li>
<li>Room upgrades</li>
<li>Professional photo session</li>
</ul>
<p>Create memories that last a lifetime in one of the world\'s most romantic destinations.</p>',
        'terms' => 'Valid for honeymoons (within 6 months of wedding) or anniversaries. Marriage certificate or anniversary verification required. Minimum 7-day package required. Subject to hotel and guide availability. 25% discount applies to tour package, excludes international flights. Romantic extras package valued at $500 included free.',
    ],

    // 5. Inactive Deal - Past Promotion
    [
        'title' => 'Black Friday Mega Sale - 40% Off All Tours',
        'active' => false,
        'start_date' => '2024-11-24 00:00:00',
        'end_date' => '2024-11-27 23:59:59',
        'discount_percentage' => 40,
        'description' => '<p>Our biggest sale of the year! Take 40% off ANY tour package during our Black Friday weekend spectacular. This is the best deal we offer all year - don\'t miss out!</p>
<p><strong>Sale Highlights:</strong></p>
<ul>
<li>40% off all tour packages</li>
<li>No minimum stay requirements</li>
<li>All destinations included</li>
<li>Flexible travel dates through 2025</li>
<li>Free cancellation up to 60 days before departure</li>
</ul>
<p>Limited spots available - book now to secure your Peru adventure at an incredible price!</p>',
        'terms' => 'Valid for bookings made November 24-27, 2024 only. Travel must be completed by December 31, 2025. 40% discount applies to tour packages only. Non-refundable $500 deposit required. Cannot be combined with other offers. Subject to availability. Blackout dates apply for Christmas and New Year periods.',
    ],

    // 6. Active Deal - Sustainable Travel Discount
    [
        'title' => 'Eco-Warrior Discount - Sustainable Tourism 10% Off',
        'active' => true,
        'start_date' => '2024-10-01 00:00:00',
        'end_date' => '2025-12-31 23:59:59',
        'discount_percentage' => 10,
        'description' => '<p>Travel sustainably and save! Our eco-friendly tours minimize environmental impact while supporting local communities. Get 10% off when you choose sustainable travel.</p>
<p><strong>Sustainable Features:</strong></p>
<ul>
<li>Carbon-neutral transportation</li>
<li>Eco-lodges and sustainable hotels</li>
<li>Community-based tourism experiences</li>
<li>Zero-waste trekking practices</li>
<li>Fair wages for all staff</li>
<li>Conservation project contributions</li>
</ul>
<p>Travel that makes a positive difference. We\'re proud to be certified by the Rainforest Alliance and committed to responsible tourism practices.</p>',
        'terms' => 'Valid for designated eco-tourism packages only. Must use reusable water bottles throughout tour. Participants commit to Leave No Trace principles. 10% discount applies year-round. Portion of proceeds supports local conservation projects. Cannot be combined with percentage-based discounts but can stack with value-adds.',
    ],

    // 7. Inactive Deal - Seasonal Winter Promotion
    [
        'title' => 'Winter Escape - Beat the Cold with 30% Off',
        'active' => false,
        'start_date' => '2024-01-15 00:00:00',
        'end_date' => '2024-03-15 23:59:59',
        'discount_percentage' => 30,
        'description' => '<p>Escape winter and discover Peru\'s warm hospitality! While it\'s freezing back home, enjoy comfortable temperatures and endless sunshine in Peru.</p>
<p><strong>Winter Escape Benefits:</strong></p>
<ul>
<li>Perfect weather for trekking</li>
<li>Smaller crowds at major sites</li>
<li>Better hotel availability</li>
<li>30% savings on all tours</li>
<li>Flexible booking policies</li>
</ul>
<p>Peru\'s dry season (May-September) offers ideal conditions for outdoor adventures. Book your winter escape now!</p>',
        'terms' => 'Valid for travel January 15 - March 15, 2024. Booking must be made at least 45 days in advance. 30% discount applies to tour packages. International flights not included. Weather conditions may vary; tours operate rain or shine. Minimum 5-day tour required.',
    ],

    // 8. Active Deal - Senior Traveler Discount
    [
        'title' => 'Senior Traveler Special - 15% Discount Ages 65+',
        'active' => true,
        'start_date' => '2024-09-01 00:00:00',
        'end_date' => '2025-12-31 23:59:59',
        'discount_percentage' => 15,
        'description' => '<p>Age is just a number! Travelers 65 and over enjoy 15% off our senior-friendly tours designed for comfort, culture, and amazing experiences without extreme physical demands.</p>
<p><strong>Senior-Friendly Features:</strong></p>
<ul>
<li>Moderate pace itineraries</li>
<li>Comfortable accommodations</li>
<li>Train to Machu Picchu (no trekking required)</li>
<li>Patient, experienced guides</li>
<li>Smaller group sizes</li>
<li>Plenty of rest time</li>
<li>Medical support available</li>
</ul>
<p>Adventure doesn\'t have an age limit. Experience Peru\'s wonders at your own comfortable pace.</p>',
        'terms' => 'Valid for travelers 65 years and older. Age verification (passport/ID) required at booking. 15% discount applies to designated senior tours only. Available year-round. Health questionnaire required. Travel insurance strongly recommended. Cannot be combined with other percentage discounts.',
    ],

    // 9. Inactive Deal - Last Minute Special
    [
        'title' => 'Last Minute Adventure - Book Within 7 Days & Save 35%',
        'active' => false,
        'start_date' => '2024-06-01 00:00:00',
        'end_date' => '2024-08-31 23:59:59',
        'discount_percentage' => 35,
        'description' => '<p>Spontaneous travelers rejoice! Book within 7 days of departure and save 35% on select tours with available space.</p>
<p><strong>Last Minute Perks:</strong></p>
<ul>
<li>35% off regular prices</li>
<li>Immediate confirmation</li>
<li>All tours fully guided</li>
<li>Same quality, lower price</li>
<li>Perfect for flexible schedules</li>
</ul>
<p>Check availability daily - spaces fill fast even for last-minute bookings!</p>',
        'terms' => 'Valid June 1 - August 31, 2024. Booking must be made 1-7 days before departure. Subject to availability only. No refunds or changes once booked. Full payment required immediately. Limited tour selection. Inca Trail permits not available last-minute. Weather-dependent activities may be modified.',
    ],

    // 10. Active Deal - Group Travel Discount
    [
        'title' => 'Group Adventures - 20% Off for Groups of 6+',
        'active' => true,
        'start_date' => '2024-10-01 00:00:00',
        'end_date' => '2025-12-31 23:59:59',
        'discount_percentage' => 20,
        'description' => '<p>Traveling with friends, family, or colleagues? Groups of 6 or more receive 20% off and can customize their itinerary to match group interests!</p>
<p><strong>Group Benefits:</strong></p>
<ul>
<li>20% discount for 6+ travelers</li>
<li>Private group departures</li>
<li>Customizable itineraries</li>
<li>Dedicated group coordinator</li>
<li>Team-building activities available</li>
<li>Group leader travels free (10+ people)</li>
<li>Flexible payment schedules</li>
</ul>
<p>Perfect for family reunions, friend groups, corporate retreats, or special interest clubs. We handle all logistics so you can focus on having fun together!</p>',
        'terms' => 'Minimum 6 travelers required. All travelers must book together. 20% discount applies to tour package base price. Group leader travels free with groups of 10+. Customization requests subject to feasibility and may incur additional costs. 60-day advance booking required. Payment schedule: 30% deposit, remainder 45 days before departure. Cancellation fees apply per individual.',
    ],
];
