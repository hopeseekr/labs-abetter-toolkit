<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\BladeDirectives;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class BladeServiceProvider extends ServiceProvider {

	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

		// Add view info to all views
		view()->composer('*', function($view){
			$view_data = new \StdClass;
			$view_data->name = $view->getName();
			$view_data->path = $view->getPath();
			view()->share('view', $view_data);
			view()->addLocation(base_path().'/vendor/abetter/toolkit/views');
			view()->addLocation(base_path().'/vendor/abetter/wordpress/views');
			view()->addNamespace('abetter',base_path().'/vendor/abetter/toolkit/views');
		});

		// Inject (extends laravel inject directive)
		Blade::directive('inject',function($expression){
			$expressions = explode(',',preg_replace("/[\(\)\\\"\']/",'',$expression));
			$variable = trim($expressions[0]);
			$service = (!empty($expressions[1])) ? trim($expressions[1]) : $variable;
			if (class_exists($service)) return "<?php \${$variable} = app('{$service}'); ?>";
			return "<?php \${$variable} = \ABetter\Toolkit\BladeDirectives::inject('{$service}',get_defined_vars()); ?>";
		});

		// Component (extends laravel component directive)
		Blade::directive('component', function($expression){
			list($path,$vars,$end) = BladeDirectives::parseExpression($expression);
			if (!\View::exists($path)) $path .= '.'.array_last(explode('.',$path)); // Test if folder
			if ($end) return "<?php global \$__vars; \$__vars = {$vars}; \$__env->startComponent('{$path}',\ABetter\Toolkit\BladeDirectives::vars(get_defined_vars(),{$vars})); ?><?php echo \$__env->renderComponent(); ?>";
			return "<?php global \$__vars; \$__vars = {$vars}; \$__env->startComponent('{$path}',\ABetter\Toolkit\BladeDirectives::vars(get_defined_vars(),{$vars})); ?>";
		});

		// Block
		Blade::directive('block', function($expression){
			list($class,$opt) = BladeDirectives::parseExpression($expression);
			return "<section class=\"component--block {$class}\">";
        });
		Blade::directive('endblock', function(){
			return "</section>";
        });

        // Style
        Blade::directive('style', function($expression){
			list($file,$vars,$link) = BladeDirectives::parseExpression($expression);
			if ($link) return "<?php echo \ABetter\Toolkit\BladeDirectives::style('{$file}',array_merge(get_defined_vars(),$vars),TRUE); ?>";
			return "<?php echo \ABetter\Toolkit\BladeDirectives::style('{$file}',array_merge(get_defined_vars(),$vars)); ?>";
        });

		// Script
        Blade::directive('script', function($expression){
			list($file,$vars,$link) = BladeDirectives::parseExpression($expression);
			if ($link) return "<?php echo \ABetter\Toolkit\BladeDirectives::script('{$file}',array_merge(get_defined_vars(),$vars),TRUE); ?>";
			return "<?php echo \ABetter\Toolkit\BladeDirectives::script('{$file}',array_merge(get_defined_vars(),$vars)); ?>";
        });

		// Svg
        Blade::directive('svg', function($expression){
			list($file,$opt) = BladeDirectives::parseExpression($expression);
			if ($opt) return "<?php echo \ABetter\Toolkit\BladeDirectives::svg('{$file}',{$opt}); ?>";
			return "<?php echo \ABetter\Toolkit\BladeDirectives::svg('{$file}'); ?>";
        });

		// Dictionary
        Blade::directive('dictionary', function($expression){
			list($type,$opt) = BladeDirectives::parseExpression($expression);
			if ($opt) return "<?php echo _dictionary('{$type}',{$opt}); ?>";
			return "<?php echo _dictionary('{$type}'); ?>";
        });

		// Lipsum
        Blade::directive('lipsum', function($expression){
			list($type,$opt) = BladeDirectives::parseExpression($expression);
			if ($opt) return "<?php echo _lipsum('{$type}',{$opt}); ?>";
			return "<?php echo _lipsum('{$type}'); ?>";
        });

		// Pixsum
        Blade::directive('pixsum', function($expression){
			list($type,$opt) = BladeDirectives::parseExpression($expression);
			if ($opt) return "<?php echo _pixsum('{$type}',{$opt}); ?>";
			return "<?php echo _pixsum('{$type}'); ?>";
        });

		// Mockup
		Blade::directive('mockup', function($expression){
			list($path,$vars,$end) = BladeDirectives::parseExpression($expression);
			$path = 'mockup.components.'.str_replace('components.',"",$path);
			if (preg_match('/abetter::/',$path)) $path = 'abetter::'.str_replace('abetter::','',$path); // Move namespace
			$end = TRUE;
			if (!\View::exists($path)) $path .= '.'.array_last(explode('.',$path)); // Test if folder
			if (!\View::exists($path)) return "<?php echo 'Mockup not found: {$path}'; ?>";
			if ($end) return "<?php \$__env->startComponent('{$path}',\ABetter\Toolkit\BladeDirectives::vars(get_defined_vars(),$vars)); ?><?php echo \$__env->renderComponent(); ?>";
			return "<?php \$__env->startComponent('{$path}',\ABetter\Toolkit\BladeDirectives::vars(get_defined_vars(),$vars)); ?>";
        });

		// LAB
		Blade::directive('lab', function($expression){
			if (!BladeDirectives::canViewLab()) return "";
			$path = 'lab.lab';
			return "<?php \$__env->startComponent('{$path}'); ?><?php echo \$__env->renderComponent(); ?>";
		});

		// Debug
        Blade::directive('debug', function($expression){
			if (!env('APP_DEBUG') && !env('WP_DEBUG')) return;
			list($message,$opt) = BladeDirectives::parseExpression($expression);
			if ($opt != '[]') return "<?php echo _debug('{$message}',{$opt}); ?>";
			return "<?php echo _debug('{$message}'); ?>";
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
