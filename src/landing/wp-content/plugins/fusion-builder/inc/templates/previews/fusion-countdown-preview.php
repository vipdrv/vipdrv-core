<script type="text/template" id="fusion-builder-block-module-countdown-preview-template">

	<div class="fusion_countdown_timer">
		<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

		<#
		var
		$countdown_end = params.countdown_end,
		$target_time = new Date(),
		$now_time = new Date();

		if ( $countdown_end === '' ) {
			var
			$secs = 0,
			$mins = 0,
			$hours = 0,
			$days = 0,
			$weeks = 0;

		} else {
			var
			$timer = $countdown_end.replace( ' ', '-' ).replace( new RegExp( ':', 'g' ), '-' ).split( '-' ),

			$target_time = new Date( $timer[1] + '/' + $timer[2] + '/' + $timer[0] + ' ' + $timer[3] + ':' + $timer[4] + ':' + $timer[5] ),

			$difference_in_secs = Math.floor( ( $target_time.valueOf() - $now_time.valueOf()) / 1000 ),

			$secs = $difference_in_secs % 60,
			$mins = Math.floor( $difference_in_secs/60 )%60,
			$hours = Math.floor( $difference_in_secs/60/60 )%24;

			if ( params.show_weeks === 'no' ) {
				var
				$days = Math.floor( $difference_in_secs/60/60/24 ),
				$weeks = Math.floor( $difference_in_secs/60/60/24/7 );
			} else {
				var
				$days = Math.floor( $difference_in_secs/60/60/24 )%7,
				$weeks = Math.floor( $difference_in_secs/60/60/24/7 );
			}
		}

		if ( isNaN( $weeks ) && isNaN( $days ) && isNaN( $hours ) && isNaN( $mins ) && isNaN( $secs ) ) { #>

			<span>Invalid date format.</span>

		<# } else {

			if ( params.show_weeks !== 'no' ) { #>
				<span><?php printf( esc_html__( '%s Weeks', 'fusion-builder' ), '{{ $weeks }}' ); ?></span>
			<# } #>

			 <span><?php printf( esc_html__( '%s Days', 'fusion-builder' ), '{{ $days }}' ); ?></span>
			 <span><?php printf( esc_html__( '%s Hrs', 'fusion-builder' ), '{{ $hours }}' ); ?></span>
			 <span><?php printf( esc_html__( '%s Min', 'fusion-builder' ), '{{ $mins }}' ); ?></span>
			 <span><?php printf( esc_html__( '%s Sec', 'fusion-builder' ), '{{ $secs }}' ); ?></span>
		<# } #>

	</div>

</script>
