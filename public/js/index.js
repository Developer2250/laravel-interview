$(document).ready(function () {
    $('#myTable').DataTable({
        ordering: false,
        responsive: true,
    });
    
    getProbabilityStatisticsData();
    
    $('#simulationForm').submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        var numberOfPrizes = $('#number_of_prizes').val();
        console.log(numberOfPrizes);

        if (numberOfPrizes.match(/^\d+$/) && parseInt(numberOfPrizes) > 0) {
           
            $.ajax({
                type: "POST",
                url: "simulate",
                data: $(this).serialize(),
                success: function(response) {
                    updateChart(response);
                    $('.awarded-reward').show();
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                }
            });
        }
        else {
            $('#error').text('Please enter a valid number of prizes.');
            $('.awarded-reward').hide();
        }
    });
});

var probabilityChart;
function updateChart(response) {
    if (probabilityChart) {
        probabilityChart.destroy();
    }

    var labels = response.labels;
    var data = response.data;
    var colors = response.colors;

    var ctx = document.getElementById('awardedChart').getContext('2d');
    probabilityChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Probability Chart',
                data: data,
                backgroundColor: colors,
                borderColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Actual Reward Chart'
                },
                datalabels: {
                    color: '#fff',
                    anchor: 'end',
                    align: 'start',
                    offset: -10,
                    borderWidth: 2,
                    borderColor: '#fff',
                    borderRadius: 25,
                    backgroundColor: (context) => {
                        return context.dataset.backgroundColor;
                    },
                    font: {
                        weight: 'bold'
                    },
                    formatter: Math.round
                }
            }
        }
    });
}

function getProbabilityStatisticsData() {
    $.ajax({
        type: "GET",
        url: "getProbabilityStatisticsData",
        async: false,
        success: function (response) {
            var labels = response.labels;
            var data = response.data;
            var colors = response.colors;
            
            var ctx = document.getElementById('probabilityChart').getContext('2d');
            var probabilityChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Probability Chart',
                        data: data,
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Probability Chart'
                        },
                        datalabels: {
                            color: '#fff',
                            anchor: 'end',
                            align: 'start',
                            offset: -10,
                            borderWidth: 2,
                            borderColor: '#fff',
                            borderRadius: 25,
                            backgroundColor: (context) => {
                                return context.dataset.backgroundColor;
                            },
                            font: {
                                weight: 'bold'
                            },
                            formatter: Math.round
                        }
                    }
                }
            });
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error(error);
        }
    });
}

