import React, { useState } from 'react';

/**
 * Componente: VinculacionWizard
 * Módulo 5: Asistente para vincular o crear perfiles con promoción a cuenta independiente
 */
const VinculacionWizard = ({ onComplete, onCancel }) => {
  const [step, setStep] = useState(1); // 1: Select Type, 2: Code/Form
  const [mode, setMode] = useState(''); // 'vincular' | 'administrado' | 'promover'
  const [codigo, setCodigo] = useState('');

  const handleVerificarCodigo = async () => {
    // Petición al backend
    alert(`Verificando código: ${codigo}`);
    onComplete();
  };

  const renderStep1 = () => (
    <div className="text-center">
      <h2 className="text-2xl font-bold mb-6">¿Qué deseas hacer?</h2>
      <div className="grid grid-cols-1 gap-4">
        <button 
          onClick={() => { setMode('vincular'); setStep(2); }}
          className="p-4 border-2 border-blue-100 rounded-xl hover:border-blue-500 text-left"
        >
          <h3 className="font-bold text-blue-700">Vincular cuenta existente</h3>
          <p className="text-sm text-gray-500">El Adulto Mayor ya tiene la app y me dio su código (PIN).</p>
        </button>

        <button 
          onClick={() => { setMode('administrado'); setStep(2); }}
          className="p-4 border-2 border-green-100 rounded-xl hover:border-green-500 text-left"
        >
          <h3 className="font-bold text-green-700">Crear perfil administrado</h3>
          <p className="text-sm text-gray-500">Yo manejaré toda la información. Él/Ella no usa celular.</p>
        </button>

        <button 
          onClick={() => { setMode('promover'); setStep(2); }}
          className="p-4 border-2 border-purple-100 rounded-xl hover:border-purple-500 text-left"
        >
          <h3 className="font-bold text-purple-700">Promover cuenta a independiente</h3>
          <p className="text-sm text-gray-500">Otorgar acceso propio (contraseña) a un perfil que yo administraba.</p>
        </button>
      </div>
      {onCancel && (
        <button onClick={onCancel} className="mt-6 text-gray-500 underline">Cancelar</button>
      )}
    </div>
  );

  const renderStep2 = () => {
    if (mode === 'vincular') {
      return (
        <div>
          <h2 className="text-2xl font-bold mb-4">Ingresa el Código PIN</h2>
          <p className="text-gray-600 mb-6">Pide al Adulto Mayor que vaya a "Mi Perfil" y te comparta el PIN de vinculación de 6 dígitos.</p>
          <input 
            type="text" 
            placeholder="Ej. AB1234" 
            className="w-full p-4 border rounded-xl mb-4 text-center text-2xl uppercase tracking-widest"
            value={codigo}
            onChange={(e) => setCodigo(e.target.value)}
          />
          <button onClick={handleVerificarCodigo} className="w-full py-3 bg-blue-600 text-white font-bold rounded-xl mb-3">Vincular Ahora</button>
          <button onClick={() => setStep(1)} className="w-full py-3 bg-gray-100 text-gray-700 rounded-xl">Volver atrás</button>
        </div>
      );
    }

    if (mode === 'administrado') {
      return (
        <div>
          <h2 className="text-2xl font-bold mb-4">Nuevo Perfil Administrado</h2>
          <form className="space-y-4">
            <input type="text" placeholder="Nombre completo del Adulto Mayor" className="w-full p-3 border rounded-lg" />
            <input type="date" className="w-full p-3 border rounded-lg" />
            <select className="w-full p-3 border rounded-lg">
              <option>Seleccionar Género</option>
              <option>Masculino</option>
              <option>Femenino</option>
            </select>
            <button type="button" onClick={onComplete} className="w-full py-3 bg-green-600 text-white font-bold rounded-xl">Crear Perfil</button>
          </form>
          <button onClick={() => setStep(1)} className="mt-4 w-full py-3 bg-gray-100 text-gray-700 rounded-xl">Volver atrás</button>
        </div>
      );
    }

    if (mode === 'promover') {
        return (
          <div>
            <h2 className="text-2xl font-bold mb-4">Promover Perfil</h2>
            <p className="text-gray-600 mb-4">Genera credenciales de acceso para que este perfil administrado pueda usarse de forma autónoma.</p>
            <form className="space-y-4">
              <select className="w-full p-3 border rounded-lg mb-2">
                <option>Selecciona un paciente administrado...</option>
                <option>Don Juan (Padre)</option>
              </select>
              <input type="tel" placeholder="Número de WhatsApp (Nuevo Login)" className="w-full p-3 border rounded-lg" />
              <input type="password" placeholder="Contraseña Temporal" className="w-full p-3 border rounded-lg" />
              <button type="button" onClick={onComplete} className="w-full py-3 bg-purple-600 text-white font-bold rounded-xl">Asignar Accesos</button>
            </form>
            <button onClick={() => setStep(1)} className="mt-4 w-full py-3 bg-gray-100 text-gray-700 rounded-xl">Volver atrás</button>
          </div>
        );
      }
  };

  return (
    <div className="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
      {step === 1 ? renderStep1() : renderStep2()}
    </div>
  );
};

export default VinculacionWizard;
