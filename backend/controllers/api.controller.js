const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

// Utilidad simulada para Webhooks (SMS/WhatsApp)
const webhookService = {
  sendWhatsAppOtp: async (phone, code) => {
    console.log(`Enviando OTP ${code} a WhatsApp: ${phone}`);
    // Integración real iría aquí (Twilio, Meta API, etc.)
  },
  triggerSOS: async (patientId) => {
    console.log(`Triggering SOS webhook para paciente: ${patientId}`);
    // Asegura el envío asíncrono y confiable del SMS/WhatsApp de emergencia
  }
};

exports.sendOtp = async (req, res) => {
  try {
    const { phone } = req.body;
    if (!phone) return res.status(400).json({ error: 'Teléfono requerido' });
    
    // Generar OTP de 6 dígitos
    const code = Math.floor(100000 + Math.random() * 900000).toString();
    const expiresAt = new Date(Date.now() + 10 * 60000); // Válido por 10 minutos
    
    await prisma.otpVerification.create({
      data: { phone, code, expiresAt }
    });
    
    await webhookService.sendWhatsAppOtp(phone, code);
    res.json({ message: 'OTP enviado correctamente' });
  } catch (error) {
    res.status(500).json({ error: 'Error enviando OTP' });
  }
};

exports.verifyOtp = async (req, res) => {
  try {
    const { phone, code } = req.body;
    const otpRecord = await prisma.otpVerification.findFirst({
      where: { phone, code, verified: false, expiresAt: { gt: new Date() } }
    });
    
    if (!otpRecord) return res.status(400).json({ error: 'Código inválido o expirado' });
    
    // Marcar OTP como verificado
    await prisma.otpVerification.update({
      where: { id: otpRecord.id },
      data: { verified: true }
    });
    
    // Actualizar usuario si existe para indicar validación de WhatsApp
    await prisma.user.updateMany({
      where: { phone },
      data: { whatsappVerified: true }
    });
    
    res.json({ message: 'WhatsApp verificado exitosamente' });
  } catch (error) {
    res.status(500).json({ error: 'Error verificando OTP' });
  }
};

exports.triggerSos = async (req, res) => {
  try {
    const { patientId } = req.body;
    if (!patientId) return res.status(400).json({ error: 'patientId requerido' });
    
    // Disparar Webhook para notificación push / WhatsApp / SMS
    await webhookService.triggerSOS(patientId);
    
    res.status(200).json({ message: 'Alerta SOS enviada correctamente y en proceso' });
  } catch (error) {
    res.status(500).json({ error: 'Fallo al enviar alerta SOS' });
  }
};

exports.undoMedication = async (req, res) => {
  try {
    const { logId } = req.params;
    const log = await prisma.medicationLog.findUnique({ where: { id: logId } });
    
    if (!log || log.status !== 'TAKEN') return res.status(400).json({ error: 'Registro inválido para deshacer' });
    
    // Ventana de deshacer: 15 minutos
    const now = new Date();
    const takenTime = new Date(log.takenAt);
    const diffMins = (now - takenTime) / (1000 * 60);
    
    if (diffMins > 15) return res.status(400).json({ error: 'El tiempo límite de 15 minutos para deshacer ha expirado' });
    
    await prisma.medicationLog.update({
      where: { id: logId },
      data: { status: 'UNDONE', takenAt: null }
    });
    
    res.json({ message: 'Toma deshecha correctamente' });
  } catch (error) {
    res.status(500).json({ error: 'Error al deshacer toma' });
  }
};
