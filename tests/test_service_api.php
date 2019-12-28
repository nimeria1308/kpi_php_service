<?php

require_once('json_service_helper.php');

function test_kpis()
{
    // Create a few KPIs
    $num_kpi = create_resource('kpi', [
        "name" => "random number",
        "type" => "number",
    ]);

    $str_kpi = create_resource('kpi', [
        "name" => "random word",
        "type" => "string",
    ]);

    # This one is going to be renamed / deleted later
    $other_kpi = create_resource('kpi', [
        "name" => "random other",
        "type" => "string",
    ]);

    # List all available
    var_dump(list_resource('kpi'));

    # Show `other KPI`
    var_dump(show_resource('kpi', $other_kpi));

    # Update name of `other KPI`
    var_dump(update_resource('kpi', $other_kpi, [
        "name" => "random WORD",
    ]));

    # Show updated name of `other KPI`
    var_dump(show_resource('kpi', $other_kpi));

    # Delete `other KPI`
    var_dump(delete_resource('kpi', $other_kpi));

    # Try requesting `Other KPI`
    # This method would throw, but we'd see
    # the error response from beind handled by send_request()
    try {
        var_dump(show_resource('kpi', $other_kpi));
    } catch (Exception $e) {
    }

    # check that it is no longer there
    var_dump(list_resource('kpi'));

    return [
        'number' => show_resource('kpi', $num_kpi),
        'string' => show_resource('kpi', $str_kpi),
    ];
}

const MOST_COMMON_WORDS = [
    'case', 'child', 'company', 'day', 'eye', 'fact', 'government', 'group', 'hand', 'life', 'man', 'number', 'part', 'person', 'place', 'point', 'problem', 'thing', 'time', 'way', 'week', 'woman', 'work', 'world', 'year',
];

function create_random_word()
{
    $idx = rand(0, count(MOST_COMMON_WORDS) - 1);
    return MOST_COMMON_WORDS[$idx];
}

function test_kpi_entries($num_kpi, $str_kpi)
{
    srand(0);

    $i = 0;
    while (true) {
        if (($i % 2) == 0) {
            # push a random number every two seconds
            create_resource('kpi_entry', [
                "data" => rand(0, 32),
            ], "kpi_id=$num_kpi->id");
        }

        if (($i % 5) == 0) {
            # push a random string every 5 seconds
            create_resource('kpi_entry', [
                "data" => create_random_word(),
            ], "kpi_id=$str_kpi->id");
        }

        if (($i % 10) == 0) {
            # Print last 5 items every 10 seconds
            $num_entries = list_resource("kpi_entry", "kpi_id=$num_kpi->id");
            $str_entries = list_resource("kpi_entry", "kpi_id=$str_kpi->id");

            # get one resource
            $entry = $num_entries[0];
            $e = show_resource("kpi_entry", $entry->id, "kpi_id=$entry->kpi_id");
            var_dump($e);

            # and delete it
            $e = delete_resource("kpi_entry", $e->id, "kpi_id=$e->kpi_id");

            var_dump(array_slice($num_entries, -2));
            var_dump(array_slice($str_entries, -2));
        }

        $i++;
        sleep(1);
    }
}

# test KPIs and make sure we have created a few new ones
$kpis = test_kpis();

# Now start pushing KPI entries
test_kpi_entries($kpis['number'], $kpis['string']);
