import React, { useState, useEffect } from 'react';

// Componente visual para feedback del usuario
const Toast = ({ message, type, onClose }) => {
  if (!message) return null;
  const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';
  return (
    <div className={`fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded shadow-lg flex items-center gap-3 z-50 animate-fade-in-up`}>
      <span className="font-medium">{message}</span>
      <button onClick={onClose} className="text-white hover:text-gray-200 font-bold focus:outline-none focus:ring-2 focus:ring-white rounded px-1">
        &times;
      </button>
    </div>
  );
};

export default function Dashboard({ user, navigate }) {
  const [patients, setPatients] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [sosLoading, setSosLoading] = useState(false);
  const [toast, setToast] = useState({ message: '', type: '' });
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    // Simulación de fetch a backend
    setTimeout(() => {
      // Mock: Familiar sin pacientes genera arreglo vacío.
      const mockPatients = user?.role === 'FAMILIAR' ? [] : [{ id: '1a2b', name: 'Roberto Gómez' }];
      setPatients(mockPatients);
      setIsLoading(false);

      if (user?.role === 'FAMILIAR' && mockPatients.length === 0) {
        // Redirección obligatoria a la Vista 5 para Familiares sin pacientes
        navigate('/vincular-paciente');
      }
    }, 1000);
  }, [user, navigate]);

  const handleSOS = async (patientId) => {
    setSosLoading(true);
    try {
      const res = await fetch('/api/sos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ patientId })
      });
      if (!res.ok) throw new Error('Ocurrió un error al contactar al servidor');
      setToast({ message: '¡Alerta SOS enviada correctamente!', type: 'success' });
    } catch (error) {
      setToast({ message: error.message, type: 'error' });
    } finally {
      setSosLoading(false);
    }
  };

  if (isLoading) {
    return (
      <div className="flex justify-center items-center h-screen bg-gray-50">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    // overflow-x-hidden para evitar scroll horizontal
    <div className="min-h-screen bg-gray-50 overflow-x-hidden flex flex-col font-sans">
      
      {/* Header Responsivo */}
      <header className="bg-blue-600 text-white p-4 flex justify-between items-center shadow-md relative z-10">
        <button onClick={() => navigate('/')} className="text-2xl font-bold tracking-tight focus:outline-none focus:ring-2 focus:ring-white rounded px-1">
          VitalSenior
        </button>
        
        {/* Hamburger Menu (Mobile) */}
        <button 
          onClick={() => setMenuOpen(!menuOpen)}
          className="md:hidden p-2 focus:outline-none focus:ring-2 focus:ring-white rounded active:bg-blue-700 transition-colors"
          aria-label="Toggle menu"
        >
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7"></path>
          </svg>
        </button>

        {/* Navbar (Desktop & Mobile Dropdown) */}
        <nav className={`${menuOpen ? 'flex' : 'hidden'} md:flex flex-col md:flex-row absolute md:relative top-full left-0 w-full md:w-auto bg-blue-600 md:bg-transparent shadow-md md:shadow-none p-4 md:p-0 gap-4 transition-all`}>
          <button onClick={() => navigate('/perfil')} className="hover:underline focus:outline-none focus:ring-2 focus:ring-white rounded p-1 text-left">
            Mi Perfil
          </button>
          <a href="tel:911" className="bg-red-500 hover:bg-red-600 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-white font-bold transition-colors text-center shadow-sm">
            Emergencia 911
          </a>
        </nav>
      </header>

      {/* Main Content */}
      <main className="flex-1 p-4 md:p-8 max-w-5xl mx-auto w-full">
        {patients.length === 0 ? (
          // Empty State
          <div className="text-center bg-white p-10 rounded-xl shadow-sm border border-gray-100 max-w-2xl mx-auto mt-10">
            <h2 className="text-2xl font-bold text-gray-800 mb-4">Aún no tienes pacientes vinculados</h2>
            <p className="text-gray-600 mb-8 leading-relaxed">
              Para comenzar a gestionar los medicamentos, recordatorios y perfiles, necesitas vincular a un paciente a tu cuenta.
            </p>
            <button 
              onClick={() => navigate('/vincular-paciente')}
              className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md focus:outline-none focus:ring-4 focus:ring-blue-300 active:scale-95 transition-all"
            >
              Vincular Paciente Ahora
            </button>
          </div>
        ) : (
          // Dashboard Activo
          <div className="space-y-6">
            <h1 className="text-3xl font-extrabold text-gray-900 mb-6">Panel de Control</h1>
            {patients.map(patient => (
              <div key={patient.id} className="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                  <div>
                    <h3 className="text-xl font-bold text-gray-800">{patient.name}</h3>
                    <p className="text-sm text-gray-500">ID Ficha: {patient.id}</p>
                  </div>
                  
                  <div className="flex flex-wrap gap-3 w-full md:w-auto">
                    <button 
                      onClick={() => handleSOS(patient.id)}
                      disabled={sosLoading}
                      className="flex-1 md:flex-none bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full shadow-md focus:outline-none focus:ring-4 focus:ring-red-300 active:bg-red-800 transition-all flex items-center justify-center min-w-[160px] disabled:opacity-75 disabled:cursor-not-allowed"
                    >
                      {sosLoading ? (
                        <span className="flex items-center gap-2">
                          <svg className="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                          Enviando...
                        </span>
                      ) : (
                        'Botón SOS'
                      )}
                    </button>
                    <button 
                      onClick={() => navigate(`/medicamentos/${patient.id}`)}
                      className="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-6 rounded-lg font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-300 active:bg-emerald-800 transition-all shadow-sm"
                    >
                      Medicación
                    </button>
                    <button 
                      onClick={() => navigate(`/expediente/${patient.id}`)}
                      className="flex-1 md:flex-none bg-gray-100 hover:bg-gray-200 text-gray-800 py-3 px-6 rounded-lg font-semibold focus:outline-none focus:ring-4 focus:ring-gray-300 active:bg-gray-300 transition-all shadow-sm"
                    >
                      Expediente
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </main>

      <Toast 
        message={toast.message} 
        type={toast.type} 
        onClose={() => setToast({ message: '', type: '' })} 
      />
    </div>
  );
}
