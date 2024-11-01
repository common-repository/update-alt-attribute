jQuery( document ).ready(function($) {
    
    // 'rgb(255, 99, 132)',   'Red',
    // 'rgb(75, 192, 192)',   'Green',
    // 'rgb(255, 205, 86)',   'Yellow',
    // 'rgb(201, 203, 207)',  'Grey',
    // 'rgb(54, 162, 235)'    'Blue'
    
    let default_config = {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                label: '',
                data: [],
                backgroundColor: [],
                hoverOffset: 4
            }]
        },
        options: {
            rotation: 0,
            cutout: '40%',
            animation: {
                animateRotate: true,
                animateScale: false,
            },
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    };
    
    if( uaa_chart_object.media_stats ){
        
        let media_stats_config = JSON.parse( JSON.stringify( default_config ) );
        
        media_stats_config.data.datasets[0].label           = 'Media Stats';
        media_stats_config.data.datasets[0].data            = uaa_chart_object.media_stats.data;
        media_stats_config.data.datasets[0].backgroundColor = uaa_chart_object.media_stats.color;
        media_stats_config.data.labels                      = uaa_chart_object.media_stats.labels;
        media_stats_config.options.rotation                 = calculate_starting_angle( uaa_chart_object.media_stats.data );
        
        const media_stats_chart = new Chart(
            document.getElementById('media_stats_chart'),
            media_stats_config
        );
        
    }
    
    
    if( uaa_chart_object.poor_attributes_stats ){
        
        let poor_attributes_stats_config = JSON.parse( JSON.stringify( default_config ) );
        
        poor_attributes_stats_config.data.datasets[0].label           = 'Poor Attributes Stats';
        poor_attributes_stats_config.data.datasets[0].data            = uaa_chart_object.poor_attributes_stats.data;
        poor_attributes_stats_config.data.datasets[0].backgroundColor = uaa_chart_object.poor_attributes_stats.color;
        poor_attributes_stats_config.data.labels                      = uaa_chart_object.poor_attributes_stats.labels;
        poor_attributes_stats_config.options.rotation                 = calculate_starting_angle( uaa_chart_object.poor_attributes_stats.data );
        
        const poor_attributes_stats_chart = new Chart(
            document.getElementById('poor_attributes_stats_chart'),
            poor_attributes_stats_config
        );
    
    }
    
    
    if( uaa_chart_object.post_type_stats ){
        
        let post_type_stats_config = JSON.parse( JSON.stringify( default_config ) );
        
        post_type_stats_config.data.datasets[0].label           = 'Poor Attributes Stats';
        post_type_stats_config.data.datasets[0].data            = uaa_chart_object.post_type_stats.data;
        post_type_stats_config.data.datasets[0].backgroundColor = uaa_chart_object.post_type_stats.color;
        post_type_stats_config.data.labels                      = uaa_chart_object.post_type_stats.labels;
        post_type_stats_config.options.rotation                 = calculate_starting_angle( uaa_chart_object.post_type_stats.data );
        
        const poor_attributes_stats_chart = new Chart(
            document.getElementById('post_type_stats_chart'),
            post_type_stats_config
        );
    
    }
    
    
    if( uaa_chart_object.used_images_stats ){
        
        let used_images_stats_config = JSON.parse( JSON.stringify( default_config ) );
        
        used_images_stats_config.data.datasets[0].label           = 'Used Images Stats';
        used_images_stats_config.data.datasets[0].data            = uaa_chart_object.used_images_stats.data;
        used_images_stats_config.data.datasets[0].backgroundColor = uaa_chart_object.used_images_stats.color;
        used_images_stats_config.data.labels                      = uaa_chart_object.used_images_stats.labels;
        used_images_stats_config.options.rotation                 = calculate_starting_angle( uaa_chart_object.used_images_stats.data );
        
        const poor_attributes_stats_chart = new Chart(
            document.getElementById('used_images_stats_chart'),
            used_images_stats_config
        );
    
    }
    
    
    function calculate_starting_angle( dataset ){
        
        if( dataset.length == 1 ){
            return 0;
        }
        
        let sum_data        = dataset.reduce( ( partialSum, a ) => partialSum + parseInt( a ), 0 );
        let colored_angle   = parseInt( sum_data ) - parseInt( dataset[0] );
        let starting_angle  = colored_angle / sum_data * 360;
        
        return starting_angle;
    }
    
    // console.log( "default_config:", default_config.data.labels );
    // console.log( "media_stats_config:", media_stats_config.data.labels );
    // console.log( "poor_attributes_stats_config:", poor_attributes_stats_config.data.labels );
    // console.log( "post_type_stats_config:", post_type_stats_config.data.labels );
    
});



