from django import forms


class MedicamentoForm(forms.Form):
    nombre_comercial = forms.CharField(min_length=2, max_length=150, required=True)
    dosis = forms.CharField(min_length=1, max_length=100, required=True)
    frecuencia_horas = forms.IntegerField(min_value=1, max_value=72, required=True, initial=8)
    instrucciones = forms.CharField(max_length=1000, required=False)
    stock_actual = forms.IntegerField(min_value=0, max_value=10000, required=True, initial=0)
    dosis_por_toma = forms.IntegerField(min_value=1, max_value=50, required=True, initial=1)
    stock_minimo_alerta = forms.IntegerField(min_value=0, max_value=1000, required=True, initial=5)
    activo = forms.BooleanField(required=False, initial=True)

