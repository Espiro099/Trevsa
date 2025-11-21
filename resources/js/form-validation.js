/**
 * Sistema de Validación en Tiempo Real para Formularios
 * Utiliza Alpine.js para crear una experiencia reactiva
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('formValidation', (rules = {}) => {
        return {
            errors: {},
            touched: {},
            values: {},
            
            init() {
                // Inicializar valores desde inputs existentes
                this.$watch('$el', () => {
                    const inputs = this.$el.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        if (input.name) {
                            this.values[input.name] = input.value || '';
                        }
                    });
                });
            },
            
            validateField(fieldName, value) {
                const fieldRules = rules[fieldName] || [];
                const fieldErrors = [];
                
                fieldRules.forEach(rule => {
                    const error = this.checkRule(rule, value, fieldName);
                    if (error) {
                        fieldErrors.push(error);
                    }
                });
                
                if (fieldErrors.length > 0) {
                    this.errors[fieldName] = fieldErrors[0]; // Mostrar solo el primer error
                } else {
                    delete this.errors[fieldName];
                }
                
                return fieldErrors.length === 0;
            },
            
            checkRule(rule, value, fieldName) {
                const { type, message, params } = rule;
                
                switch (type) {
                    case 'required':
                        if (!value || value.trim() === '') {
                            return message || `${this.getFieldLabel(fieldName)} es requerido`;
                        }
                        break;
                        
                    case 'minLength':
                        if (value && value.length < params.min) {
                            return message || `${this.getFieldLabel(fieldName)} debe tener al menos ${params.min} caracteres`;
                        }
                        break;
                        
                    case 'maxLength':
                        if (value && value.length > params.max) {
                            return message || `${this.getFieldLabel(fieldName)} no puede exceder ${params.max} caracteres`;
                        }
                        break;
                        
                    case 'email':
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (value && !emailRegex.test(value)) {
                            return message || 'El email no es válido';
                        }
                        break;
                        
                    case 'phone':
                        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                        if (value && !phoneRegex.test(value)) {
                            return message || 'El teléfono no es válido';
                        }
                        break;
                        
                    case 'numeric':
                        if (value && isNaN(value)) {
                            return message || `${this.getFieldLabel(fieldName)} debe ser un número`;
                        }
                        break;
                        
                    case 'min':
                        if (value && parseFloat(value) < params.min) {
                            return message || `${this.getFieldLabel(fieldName)} debe ser mayor o igual a ${params.min}`;
                        }
                        break;
                        
                    case 'max':
                        if (value && parseFloat(value) > params.max) {
                            return message || `${this.getFieldLabel(fieldName)} debe ser menor o igual a ${params.max}`;
                        }
                        break;
                        
                    case 'custom':
                        if (params.validator && !params.validator(value)) {
                            return message || params.errorMessage || 'El valor no es válido';
                        }
                        break;
                }
                
                return null;
            },
            
            getFieldLabel(fieldName) {
                // Intentar obtener el label del campo
                const label = this.$el.querySelector(`label[for="${fieldName}"]`);
                if (label) {
                    return label.textContent.replace('*', '').trim();
                }
                // Convertir nombre de campo a label legible
                return fieldName
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, l => l.toUpperCase());
            },
            
            handleInput(fieldName, event) {
                const value = event.target.value;
                this.values[fieldName] = value;
                this.touched[fieldName] = true;
                this.validateField(fieldName, value);
            },
            
            handleBlur(fieldName, event) {
                this.touched[fieldName] = true;
                this.validateField(fieldName, event.target.value);
            },
            
            hasError(fieldName) {
                return this.touched[fieldName] && this.errors[fieldName];
            },
            
            getError(fieldName) {
                return this.errors[fieldName];
            },
            
            isValid() {
                // Validar todos los campos
                Object.keys(rules).forEach(fieldName => {
                    if (!this.touched[fieldName]) {
                        this.touched[fieldName] = true;
                    }
                    this.validateField(fieldName, this.values[fieldName] || '');
                });
                
                return Object.keys(this.errors).length === 0;
            },
            
            validateForm() {
                if (!this.isValid()) {
                    // Scroll al primer error
                    const firstError = this.$el.querySelector('.form-error:first-of-type');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
                return true;
            }
        };
    });
});

/**
 * Helpers para validación común
 */
window.FormValidationHelpers = {
    required: (message) => ({ type: 'required', message }),
    minLength: (min, message) => ({ type: 'minLength', params: { min }, message }),
    maxLength: (max, message) => ({ type: 'maxLength', params: { max }, message }),
    email: (message) => ({ type: 'email', message }),
    phone: (message) => ({ type: 'phone', message }),
    numeric: (message) => ({ type: 'numeric', message }),
    min: (min, message) => ({ type: 'min', params: { min }, message }),
    max: (max, message) => ({ type: 'max', params: { max }, message }),
};

/**
 * Inicialización automática para formularios con data-validate
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const validation = Alpine.$data(form.querySelector('[x-data*="formValidation"]'));
            if (validation && !validation.validateForm()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
});

