from django import forms
from django.core.validators import EmailValidator, RegexValidator


class EmailStepForm(forms.Form):
    email = forms.EmailField(
        max_length=255,
        required=True,
        validators=[EmailValidator(message="Ingresa un correo electrónico con formato válido.")],
    )

    def clean_email(self):
        return self.cleaned_data['email'].strip().lower()


class PasswordStepForm(forms.Form):
    password = forms.CharField(
        min_length=1,
        max_length=128,
        required=True,
        strip=False,
    )


class OTPCodeForm(forms.Form):
    otp_code = forms.CharField(
        min_length=6,
        max_length=16,
        required=True,
        validators=[
            RegexValidator(
                regex=r'^[0-9A-Za-z\s\-]{6,16}$',
                message="Ingresa un código válido de 6 dígitos.",
            )
        ],
    )

    def clean_otp_code(self):
        raw = self.cleaned_data['otp_code'].strip().replace(' ', '')
        return raw


class RegistroUsuarioForm(forms.Form):
    ROLES_PERMITIDOS_REGISTRO_PUBLICO = ('familiar_admin', 'paciente')

    nombre = forms.CharField(min_length=2, max_length=255, required=True)
    email = forms.EmailField(max_length=255, required=True)
    telefono = forms.CharField(max_length=20, required=False)
    password = forms.CharField(min_length=8, max_length=128, required=True, strip=False)
    password_confirm = forms.CharField(min_length=8, max_length=128, required=True, strip=False)
    rol = forms.CharField(max_length=50, required=False)

    def clean_email(self):
        return self.cleaned_data['email'].strip().lower()

    def clean_rol(self):
        # Zero Trust: Ignorar cualquier rol privilegiado inyectado desde Inspeccionar Elemento (ej. 'administrador')
        rol = (self.cleaned_data.get('rol') or 'familiar_admin').strip()
        if rol not in self.ROLES_PERMITIDOS_REGISTRO_PUBLICO:
            return 'familiar_admin'
        return rol

    def clean(self):
        cleaned = super().clean()
        pwd = cleaned.get('password')
        pwd_confirm = cleaned.get('password_confirm')
        if pwd and pwd_confirm and pwd != pwd_confirm:
            raise forms.ValidationError("Las contraseñas no coinciden.")
        return cleaned

