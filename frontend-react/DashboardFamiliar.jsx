import React, { useState, useEffect } from 'react';
import VinculacionWizard from './VinculacionWizard';

/**
 * Componente: DashboardFamiliar
 * Módulo 4: Dashboard con manejo de Empty State (Usuario sin pacientes vinculados)
 */
const DashboardFamiliar = ({ user }) => {
  const [pacientes, setPacientes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showVinculacion, setShowVinculacion] = useState(false);

  useEffect(() => {
    // Fetch pacientes vinculados al familiar
    fetch('/api/familiares/pacientes')
      .then(res => res.json())
      .then(data => {
        setPacientes(data.pacientes || []);
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        setLoading(false);
      });
  }, []);

  if (loading) return <div className="p-8 text-center">Cargando dashboard...</div>;

  // ==== EMPTY STATE OBLIGATORIO ====
  if (pacientes.length === 0 || showVinculacion) {
    return (
      <div className="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6">
        {!showVinculacion ? (
          <div className="bg-white rounded-2xl shadow-xl p-8 max-w-lg text-center">
            <img src="/assets/empty-family.svg" alt="Sin pacientes" className="w-48 mx-auto mb-6 opacity-80" />
            <h2 className="text-2xl font-bold text-gray-800 mb-4">Bienvenido, {user.nombre}</h2>
            <p className="text-gray-600 mb-8">
              Actualmente no administras el expediente de ningún Adulto Mayor. Para comenzar a recibir notificaciones médicas y monitorear su salud, necesitas vincular o crear un perfil.
            </p>
            <button 
              onClick={() => setShowVinculacion(true)}
              className="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition"
            >
              Comenzar Vinculación
            </button>
          </div>
        ) : (
          <VinculacionWizard onComplete={() => window.location.reload()} onCancel={() => setShowVinculacion(false)} />
        )}
      </div>
    );
  }

  // ==== DASHBOARD PRINCIPAL ====
  return (
    <div className="p-6 max-w-6xl mx-auto">
      <header className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold text-gray-800">Panel Familiar</h1>
        <button 
          onClick={() => setShowVinculacion(true)}
          className="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
        >
          + Vincular otro Paciente
        </button>
      </header>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {pacientes.map(paciente => (
          <div key={paciente.id} className="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <h3 className="text-xl font-bold">{paciente.nombre_completo}</h3>
            <p className="text-sm text-gray-500 mb-4">ID Público: {paciente.uuid_publico}</p>
            
            <div className="flex gap-2">
              <a href={`/pacientes/${paciente.id}/expediente`} className="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Ver Expediente</a>
              <a href={`/pacientes/${paciente.id}/medicamentos`} className="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Medicamentos</a>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default DashboardFamiliar;
