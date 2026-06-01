<?php
declare(strict_types=1);
namespace WebFiori\Cli\Templates;

/**
 * Template manager for command scaffolding.
 */
class TemplateManager {
    
    private string $templatesPath;
    
    public function __construct(?string $templatesPath = null) {
        $this->templatesPath = $templatesPath ?? __DIR__ . '/stubs';
    }
    
    /**
     * Get template content by name.
     */
    public function getTemplate(string $name): string {
        $templateFile = $this->templatesPath . '/' . $name . '.stub';
        
        if (!file_exists($templateFile)) {
            throw new \InvalidArgumentException("Template '$name' not found at: $templateFile");
        }
        
        return file_get_contents($templateFile);
    }
    
    /**
     * Get available templates.
     */
    public function getAvailableTemplates(): array {
        $templates = [];
        $files = glob($this->templatesPath . '/*.stub');
        
        foreach ($files as $file) {
            $templates[] = basename($file, '.stub');
        }
        
        return $templates;
    }
    
    /**
     * Process template with variables.
     */
    public function processTemplate(string $template, array $variables): string {
        $content = $this->getTemplate($template);
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }
}
