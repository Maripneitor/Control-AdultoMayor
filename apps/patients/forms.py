from datetime import date
from django import forms
from django.core.validators import RegexValidator


class PacienteForm(forms.Form):
    TIPOS_SANGRE_VALIDOS = ('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'N/D')

    nombre = forms.CharField(min_length=2, max_length=100, required=True)
    apellidos = forms.CharField(min_length=2, max_length=100, required=True)
    fecha_nacimiento = forms.DateField(required=True)
    genero = forms.CharField(max_length=20, required=False)
    tipo_sangre = forms.CharField(max_length=5, required=False)
    alergias = forms.CharField(max_length=2000, required=False)
    enfermedades_cronicas = forms.CharField(max_length=2000, required=False)
    notas_cognitivas = forms.CharField(max_length=2000, required=False)

    def clean_fecha_nacimiento(self):
        fn = self.cleaned_data['fecha_nacimiento']
        hoy = date.today()
        if fn > hoy or fn.year < 1900:
            raise forms.ValidationError("Ingresa una fecha de nacimiento válida.")
        return fn

    def clean_tipo_sangre(self):
        ts = (self.cleaned_data.get('tipo_sangre') or 'N/D').strip().upper()
        if ts not in self.TIPOS_SANGRE_VALIDOS:
            return 'N/D'
        return ts


class ContactoEmergenciaForm(forms.Form):
    nombre = forms.CharField(min_length=2, max_length=150, required=True)
    parentesco = forms.CharField(max_length=50, required=False)
    telefono = forms.CharField(
        min_length=7,
        max_length=25,
        required=True,
        validators=[
            RegexValidator(
                regex=r'^[0-9\+\-\s\(\)]{7,25}$',
                message="Ingresa un número telefónico válido.",
            )
        ],
    )
    es_prioritario = forms.BooleanField(required=False)

