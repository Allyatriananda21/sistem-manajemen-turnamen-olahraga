import React, { useState, useRef } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'motion/react';
import axios from 'axios';
import { PlusCircle, Upload, ShieldCheck, AlertTriangle, ArrowLeft, Loader2, CheckCircle2 } from 'lucide-react';
import API_BASE_URL from '../config/api';

interface FormErrors {
  name?: string;
  sport_type?: string;
  coach_name?: string;
  contact_person?: string;
  phone?: string;
  logo?: string;
}

export default function Register() {
  const [formData, setFormData] = useState({
    name: '',
    sport_type: 'Sepak Bola', // Default selected
    coach_name: '',
    contact_person: '',
    phone: '',
  });

  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [logoPreview, setLogoPreview] = useState<string>('');
  
  const [errors, setErrors] = useState<FormErrors>({});
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [registeredTeam, setRegisteredTeam] = useState<any>(null);

  const fileInputRef = useRef<HTMLInputElement>(null);

  // Handle text input changes
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    // Clear field-specific error as user types
    if (errors[name as keyof FormErrors]) {
      setErrors((prev) => ({ ...prev, [name]: undefined }));
    }
  };

  // Handle Logo file upload
  const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    // Reset logo errors
    setErrors((prev) => ({ ...prev, logo: undefined }));

    // File validation (image format and max 2MB size)
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!validTypes.includes(file.type)) {
      setErrors((prev) => ({ ...prev, logo: 'Logo harus berupa berkas gambar (JPG, PNG, atau WEBP).' }));
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      setErrors((prev) => ({ ...prev, logo: 'Ukuran logo maksimal adalah 2 MB.' }));
      return;
    }

    setLogoFile(file);
    const reader = new FileReader();
    reader.onloadend = () => {
      setLogoPreview(reader.result as string);
    };
    reader.readAsDataURL(file);
  };

  // Perform client-side validation
  const validateForm = (): boolean => {
    const newErrors: FormErrors = {};

    if (!formData.name.trim()) {
      newErrors.name = 'Nama tim wajib diisi.';
    } else if (formData.name.length > 100) {
      newErrors.name = 'Nama tim maksimal 100 karakter.';
    }

    if (!formData.sport_type.trim()) {
      newErrors.sport_type = 'Cabang olahraga wajib diisi.';
    } else if (formData.sport_type.length > 50) {
      newErrors.sport_type = 'Cabang olahraga maksimal 50 karakter.';
    }

    if (formData.coach_name && formData.coach_name.length > 100) {
      newErrors.coach_name = 'Nama pelatih maksimal 100 karakter.';
    }

    if (!formData.contact_person.trim()) {
      newErrors.contact_person = 'Contact person wajib diisi.';
    } else if (formData.contact_person.length > 100) {
      newErrors.contact_person = 'Contact person maksimal 100 karakter.';
    }

    if (!formData.phone.trim()) {
      newErrors.phone = 'Nomor telepon wajib diisi.';
    } else if (formData.phone.length > 20) {
      newErrors.phone = 'Nomor telepon maksimal 20 karakter.';
    } else if (!/^[0-9+-\s]+$/.test(formData.phone)) {
      newErrors.phone = 'Nomor telepon tidak valid (hanya angka).';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Handle Form Submission
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateForm()) return;

    setLoading(true);
    setErrors({});

    // Build FormData (Multipart/form-data request)
    const submissionData = new FormData();
    submissionData.append('name', formData.name.trim());
    submissionData.append('sport_type', formData.sport_type.trim());
    submissionData.append('coach_name', formData.coach_name.trim());
    submissionData.append('contact_person', formData.contact_person.trim());
    submissionData.append('phone', formData.phone.trim());
    if (logoFile) {
      submissionData.append('logo', logoFile);
    }

    try {
      const response = await axios.post(`${API_BASE_URL}/teams/register`, submissionData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      setRegisteredTeam(response.data?.data || response.data);
      setSuccess(true);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err: any) {
      console.error('Error registering team:', err);
      if (err.response?.status === 422) {
        // Map Laravel backend validation errors
        const validationErrors = err.response.data?.errors || {};
        const backendErrors: FormErrors = {};
        
        Object.keys(validationErrors).forEach((key) => {
          if (Array.isArray(validationErrors[key])) {
            backendErrors[key as keyof FormErrors] = validationErrors[key][0];
          }
        });
        
        setErrors(backendErrors);
        
        // Show general error message if needed
        if (err.response.data?.message) {
          setErrors(prev => ({ ...prev, name: prev.name || err.response.data.message }));
        }
      } else {
        setErrors({
          name: err.response?.data?.message || err.message || 'Terjadi kesalahan sistem. Silakan coba lagi nanti.',
        });
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div id="register-page" className="relative min-h-screen bg-transparent pt-28 pb-24 px-4 sm:px-6 lg:px-8">
      {/* Background radial glow */}
      <div className="absolute top-20 left-1/2 -translate-x-1/2 w-full max-w-5xl h-[500px] bg-primary/10 rounded-full blur-[120px] pointer-events-none" />

      <div className="max-w-3xl mx-auto">
        
        {/* Navigation back */}
        <Link to="/" className="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-accent font-medium mb-6 transition-colors group">
          <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
          Kembali ke Beranda
        </Link>

        {success ? (
          /* SUCCESS STATE PANEL */
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5 }}
            className="glass-card p-8 md:p-12 rounded-3xl border border-emerald-500/20 text-center space-y-6 shadow-2xl relative overflow-hidden"
          >
            {/* Glowing top element */}
            <div className="absolute top-0 inset-x-0 h-[4px] bg-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.5)]" />

            <div className="w-16 h-16 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto animate-bounce">
              <CheckCircle2 className="w-10 h-10" />
            </div>

            <div className="space-y-2">
              <h2 className="font-display text-2xl md:text-3xl font-extrabold uppercase text-white">
                Tim berhasil didaftarkan!
              </h2>
              <p className="text-slate-300 text-sm max-w-lg mx-auto">
                Status: <span className="text-[#E4FD97] font-bold">Menunggu Persetujuan Panitia</span>
              </p>
              <p className="text-slate-400 text-xs max-w-md mx-auto mt-2">
                Tim <span className="text-white font-bold">{registeredTeam?.name || formData.name}</span> telah masuk ke sistem kami untuk ditinjau oleh panitia.
              </p>
            </div>

            <div className="bg-primary/20 border border-white/5 rounded-2xl p-6 text-left max-w-lg mx-auto space-y-4">
              <div className="flex gap-3 items-start text-xs md:text-sm text-slate-300">
                <div className="p-1 bg-[#E4FD97]/10 border border-[#E4FD97]/20 rounded text-[#E4FD97] font-bold">1</div>
                <div>
                  <h4 className="font-semibold text-white">Menunggu Persetujuan Panitia</h4>
                  <p className="text-slate-400 text-xs mt-0.5">Admin akan memverifikasi kelengkapan berkas serta keaslian kontak pendaftar.</p>
                </div>
              </div>
              <div className="flex gap-3 items-start text-xs md:text-sm text-slate-300">
                <div className="p-1 bg-[#E4FD97]/10 border border-[#E4FD97]/20 rounded text-[#E4FD97] font-bold">2</div>
                <div>
                  <h4 className="font-semibold text-white">Konfirmasi & Pembayaran</h4>
                  <p className="text-slate-400 text-xs mt-0.5">Setelah disetujui, panitia akan mengirimkan petunjuk pembayaran kontribusi pendaftaran.</p>
                </div>
              </div>
            </div>

            <div className="pt-6 flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                to="/"
                className="px-6 py-3 bg-[#E4FD97] text-[#2D3E2C] font-display font-bold rounded-xl text-sm transition-all hover:bg-[#E4FD97]/95 shadow-lg shadow-[0_0_15px_rgba(228,253,151,0.3)] active:scale-95"
              >
                Kembali ke Beranda
              </Link>
              <Link
                to="/teams"
                className="px-6 py-3 bg-white/5 border border-white/10 text-white font-display font-semibold rounded-xl text-sm transition-all hover:bg-white/10 active:scale-95"
              >
                Lihat Daftar Tim
              </Link>
              <button
                onClick={() => {
                  setFormData({ name: '', sport_type: 'Sepak Bola', coach_name: '', contact_person: '', phone: '' });
                  setLogoFile(null);
                  setLogoPreview('');
                  setSuccess(false);
                  setRegisteredTeam(null);
                }}
                className="px-6 py-3 bg-white/5 border border-white/10 text-slate-300 font-display font-semibold rounded-xl text-sm transition-all hover:bg-white/10 hover:text-white active:scale-95"
              >
                Daftar Tim Lain
              </button>
            </div>
          </motion.div>
        ) : (
          /* FORM SUBMISSION PANEL */
          <div className="glass-card rounded-3xl border border-white/5 p-6 md:p-10 space-y-8 shadow-2xl relative">
            <div className="space-y-2">
              <span className="text-xs font-mono font-bold tracking-wider text-accent uppercase flex items-center gap-1.5">
                <PlusCircle className="w-4 h-4 text-accent" />
                Formulir Pendaftaran
              </span>
              <h2 className="font-display text-2xl md:text-3xl font-extrabold uppercase text-white">
                DAFTARKAN SKUAD ANDA
              </h2>
              <p className="text-slate-400 text-sm">
                Isi data dengan lengkap dan unggah lambang kebesaran tim. Tim baru akan melalui proses peninjauan oleh admin turnamen.
              </p>
            </div>

            {/* General alert for errors */}
            {Object.keys(errors).length > 0 && (
              <div className="p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl flex gap-3 items-center text-sm">
                <AlertTriangle className="w-5 h-5 shrink-0" />
                <span>Harap periksa kembali isian formulir Anda yang berwarna merah di bawah ini.</span>
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
              
              {/* Logo Upload & Preview Section */}
              <div className="flex flex-col sm:flex-row items-center gap-6 pb-4 border-b border-white/5">
                <div className="relative shrink-0">
                  {logoPreview ? (
                    <img
                      src={logoPreview}
                      alt="Preview logo"
                      referrerPolicy="no-referrer"
                      className="w-24 h-24 rounded-full object-cover border-2 border-accent bg-primary/20"
                    />
                  ) : (
                    <div className="w-24 h-24 rounded-full bg-white/5 border border-dashed border-white/20 flex flex-col items-center justify-center text-slate-500">
                      <Upload className="w-6 h-6 mb-1" />
                      <span className="text-[10px] font-medium uppercase font-mono">NO LOGO</span>
                    </div>
                  )}
                  {logoPreview && (
                    <button
                      type="button"
                      onClick={() => { setLogoFile(null); setLogoPreview(''); }}
                      className="absolute -top-1 -right-1 w-6 h-6 bg-red-600 rounded-full text-white text-xs font-bold hover:bg-red-500 flex items-center justify-center"
                    >
                      &times;
                    </button>
                  )}
                </div>

                <div className="space-y-2 text-center sm:text-left">
                  <h4 className="font-display font-semibold text-sm text-white">Logo Tim (Opsional)</h4>
                  <p className="text-xs text-slate-400 max-w-sm">
                    Format gambar (JPG, PNG, atau WEBP). Ukuran berkas maksimal 2 Megabytes.
                  </p>
                  <button
                    type="button"
                    onClick={() => fileInputRef.current?.click()}
                    className="mt-1 inline-flex items-center gap-1.5 px-4 py-2 bg-primary/80 border border-white/10 rounded-lg text-xs font-medium text-white hover:bg-primary transition-all"
                  >
                    <Upload className="w-3.5 h-3.5 text-accent" />
                    Pilih File Gambar
                  </button>
                  <input
                    type="file"
                    ref={fileInputRef}
                    onChange={handleLogoChange}
                    accept="image/*"
                    className="hidden"
                  />
                  {errors.logo && <p className="text-red-400 text-xs mt-1 font-medium">{errors.logo}</p>}
                </div>
              </div>

              {/* Text Fields */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {/* Team Name */}
                <div className="space-y-1.5">
                  <label htmlFor="name" className="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Nama Tim <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    value={formData.name}
                    onChange={handleChange}
                    placeholder="Contoh: Garuda Futsal Club"
                    className={`w-full bg-dark-bg/60 border ${errors.name ? 'border-red-500/50' : 'border-white/10'} rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-accent/50 focus:ring-1 focus:ring-accent/30 transition-all`}
                  />
                  {errors.name && <p className="text-red-400 text-xs font-medium">{errors.name}</p>}
                </div>

                {/* Sport Type Dropdown */}
                <div className="space-y-1.5">
                  <label htmlFor="sport_type" className="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Kategori Olahraga <span className="text-red-500">*</span>
                  </label>
                  <select
                    id="sport_type"
                    name="sport_type"
                    value={formData.sport_type}
                    onChange={handleChange}
                    className="w-full bg-dark-bg/60 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:border-accent/50 focus:ring-1 focus:ring-accent/30 transition-all [&>option]:bg-dark-bg [&>option]:text-white"
                  >
                    <option value="Futsal">Futsal</option>
                    <option value="Basket">Basket</option>
                    <option value="Voli">Voli</option>
                    <option value="Badminton">Badminton</option>
                    <option value="Sepak Bola">Sepak Bola</option>
                    <option value="Lainnya">Lainnya</option>
                  </select>
                  {errors.sport_type && <p className="text-red-400 text-xs font-medium">{errors.sport_type}</p>}
                </div>

                {/* Coach Name (Optional) */}
                <div className="space-y-1.5">
                  <label htmlFor="coach_name" className="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Nama Pelatih (Opsional)
                  </label>
                  <input
                    type="text"
                    id="coach_name"
                    name="coach_name"
                    value={formData.coach_name}
                    onChange={handleChange}
                    placeholder="Nama Pelatih Tim"
                    className={`w-full bg-dark-bg/60 border ${errors.coach_name ? 'border-red-500/50' : 'border-white/10'} rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-accent/50 focus:ring-1 focus:ring-accent/30 transition-all`}
                  />
                  {errors.coach_name && <p className="text-red-400 text-xs font-medium">{errors.coach_name}</p>}
                </div>

                {/* Contact Person Name */}
                <div className="space-y-1.5">
                  <label htmlFor="contact_person" className="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Nama Penanggung Jawab / CP <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="contact_person"
                    name="contact_person"
                    value={formData.contact_person}
                    onChange={handleChange}
                    placeholder="Nama Kontak Utama"
                    className={`w-full bg-dark-bg/60 border ${errors.contact_person ? 'border-red-500/50' : 'border-white/10'} rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-accent/50 focus:ring-1 focus:ring-accent/30 transition-all`}
                  />
                  {errors.contact_person && <p className="text-red-400 text-xs font-medium">{errors.contact_person}</p>}
                </div>

                {/* Phone Number */}
                <div className="space-y-1.5 md:col-span-2">
                  <label htmlFor="phone" className="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Nomor WhatsApp / No. Telepon <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="phone"
                    name="phone"
                    value={formData.phone}
                    onChange={handleChange}
                    placeholder="Contoh: 081234567890"
                    className={`w-full bg-dark-bg/60 border ${errors.phone ? 'border-red-500/50' : 'border-white/10'} rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-accent/50 focus:ring-1 focus:ring-accent/30 transition-all`}
                  />
                  {errors.phone && <p className="text-red-400 text-xs font-medium">{errors.phone}</p>}
                </div>
              </div>

              {/* Rules & Integrity Reminder */}
              <div className="p-4 bg-primary/20 border border-white/5 rounded-xl text-xs text-slate-400 leading-relaxed flex gap-3 items-start">
                <ShieldCheck className="w-4 h-4 text-accent shrink-0 pt-0.5" />
                <span>
                  Dengan menekan tombol "Kirim Pendaftaran", Anda menyatakan bahwa seluruh data yang diisi benar adanya dan bertanggung jawab penuh selaku perwakilan resmi tim.
                </span>
              </div>

              {/* Submit Buttons */}
              <div className="flex flex-col sm:flex-row gap-4 pt-4 border-t border-white/5 justify-end">
                <Link
                  to="/"
                  className="px-6 py-3 border border-white/10 text-slate-300 rounded-xl hover:text-white hover:bg-white/5 text-center font-medium text-sm transition-all"
                >
                  Batal
                </Link>
                <button
                  type="submit"
                  disabled={loading}
                  className="px-8 py-3 bg-accent text-dark-bg font-display font-bold rounded-xl text-sm transition-all hover:bg-accent-hover flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(228,253,151,0.3)] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {loading ? (
                    <>
                      <Loader2 className="w-4 h-4 animate-spin" />
                      Memproses...
                    </>
                  ) : (
                    <>
                      Kirim Pendaftaran Tim
                    </>
                  )}
                </button>
              </div>
            </form>
          </div>
        )}
      </div>
    </div>
  );
}
