from django import forms


class RegistroSaludForm(forms.Form):
    MOMENTOS_VALIDOS = ('ayunas', 'post_comida', 'antes_dormir', 'otro')

    presion_sistolica = forms.IntegerField(min_value=40, max_value=300, required=False)
    presion_diastolica = forms.IntegerField(min_value=20, max_value=200, required=False)
    ritmo_cardiaco = forms.IntegerField(min_value=25, max_value=250, required=False)
    glucosa = forms.FloatField(min_value=10.0, max_value=800.0, required=False)
    momento_glucosa = forms.CharField(max_length=30, required=False)
    horas_sueno = forms.FloatField(min_value=0.0, max_value=24.0, required=False)
    nivel_dolor = forms.IntegerField(min_value=0, max_value=10, required=False, initial=0)
    peso = forms.FloatField(min_value=15.0, max_value=300.0, required=False)
    altura = forms.FloatField(min_value=50.0, max_value=250.0, required=False)
    notas = forms.CharField(max_length=1500, required=False)

    def clean_momento_glucosa(self):
        m = (self.cleaned_data.get('momento_glucosa') or 'ayunas').strip().lower()
        return m if m in self.MOMENTOS_VALIDOS else 'ayunas'

