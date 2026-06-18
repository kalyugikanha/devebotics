import { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';

function MathCaptcha({ onValid }: { onValid: (valid: boolean) => void }) {
  const [num1, setNum1] = useState(0);
  const [num2, setNum2] = useState(0);
  const [ans, setAns] = useState('');
  const [shake, setShake] = useState(false);
  const canvasRef = useRef<HTMLCanvasElement>(null);

  const generate = () => {
    const n1 = Math.floor(Math.random() * 10) + 1;
    const n2 = Math.floor(Math.random() * 10) + 1;
    setNum1(n1); setNum2(n2); setAns(''); onValid(false);
    drawCanvas(n1, n2);
  };

  const drawCanvas = (n1: number, n2: number) => {
    const cvs = canvasRef.current;
    if (!cvs) return;
    const ctx = cvs.getContext('2d');
    if (!ctx) return;
    ctx.clearRect(0, 0, cvs.width, cvs.height);
    // Background noise
    ctx.fillStyle = '#111';
    ctx.fillRect(0, 0, cvs.width, cvs.height);
    for (let i = 0; i < 50; i++) {
      ctx.fillStyle = `rgba(255,255,255,${Math.random() * 0.05})`;
      ctx.beginPath();
      ctx.arc(Math.random() * cvs.width, Math.random() * cvs.height, Math.random() * 2, 0, Math.PI * 2);
      ctx.fill();
    }
    // Text
    ctx.font = 'bold 24px "Montserrat", sans-serif';
    ctx.textBaseline = 'middle';
    ctx.textAlign = 'center';
    const text = `What is ${n1} + ${n2} ?`;
    ctx.save();
    ctx.translate(cvs.width / 2, cvs.height / 2);
    ctx.rotate((Math.random() - 0.5) * 0.1);
    ctx.fillStyle = '#111827';
    ctx.fillText(text, 0, 0);
    ctx.restore();
  };

  useEffect(() => { generate(); }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const v = e.target.value;
    setAns(v);
    if (v === String(num1 + num2)) {
      onValid(true);
      setShake(false);
    } else {
      onValid(false);
      if (v.length >= String(num1 + num2).length) {
        setShake(true);
        setTimeout(() => setShake(false), 400);
      }
    }
  };

  return (
    <div style={{ background: '#F9FAFB', border: '1px solid #E5E7EB', padding: 20, borderRadius: 8 }}>
      <div style={{ display: 'flex', gap: 16, alignItems: 'center', marginBottom: 16 }}>
        <canvas ref={canvasRef} width={200} height={60} style={{ borderRadius: 4, border: '1px solid #E5E7EB' }} />
        <button type="button" onClick={generate} style={{ background: 'transparent', border: 'none', cursor: 'pointer', padding: 8, color: '#4B5563', transition: 'color 0.2s, transform 0.4s' }}
          onMouseEnter={e => { e.currentTarget.style.color = '#111827'; e.currentTarget.style.transform = 'rotate(180deg)'; }}
          onMouseLeave={e => { e.currentTarget.style.color = '#4B5563'; e.currentTarget.style.transform = 'rotate(0deg)'; }}>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/><path d="M3 2v6h6"/></svg>
        </button>
      </div>
      <div style={{ position: 'relative' }}>
        <input type="text" value={ans} onChange={handleChange} placeholder="Your answer"
          style={{ width: '100%', background: 'transparent', border: 'none', borderBottom: `1px solid ${shake ? '#FF4747' : '#E5E7EB'}`, padding: '10px 0', fontFamily: 'Lexend, sans-serif', fontSize: 16, color: '#111827', outline: 'none', transition: 'border-color 0.2s', animation: shake ? 'shake 0.4s' : 'none' }}
          onFocus={e => !shake && (e.target.style.borderBottomColor = '#1F8844')}
          onBlur={e => !shake && (e.target.style.borderBottomColor = '#E5E7EB')}
        />
        {ans === String(num1 + num2) && <span style={{ position: 'absolute', right: 0, top: 12, color: '#1F8844', animation: 'pulse-dot 1s infinite' }}>✓</span>}
      </div>
      <style>{`@keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-4px)} 75%{transform:translateX(4px)} }`}</style>
    </div>
  );
}

function LiveClock({ tz, label }: { tz: string; label: string }) {
  const [time, setTime] = useState('');
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    const update = () => {
      const d = new Date();
      const str = d.toLocaleTimeString('en-US', { timeZone: tz, hour: '2-digit', minute: '2-digit' });
      setTime(str);
      const h = parseInt(d.toLocaleTimeString('en-US', { timeZone: tz, hour: 'numeric', hour12: false }));
      setIsOpen(h >= 9 && h < 18);
    };
    update();
    const iv = setInterval(update, 1000);
    return () => clearInterval(iv);
  }, [tz]);

  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: 8, fontFamily: 'Lexend, monospace', fontSize: 13, color: '#9CA3AF' }}>
      <span style={{ width: 6, height: 6, borderRadius: '50%', background: isOpen ? '#1F8844' : '#9CA3AF', transition: 'background 0.5s' }} />
      <span style={{ width: 50 }}>{label}</span>
      <span>{time}</span>
    </div>
  );
}

export default function ContactMain() {
  const [step, setStep] = useState(1);
  const [formData, setFormData] = useState({ name: '', email: '', phone: '', services: [] as string[], budget: '', timeline: '', desc: '', file: null as File | null });
  const [captchaValid, setCaptchaValid] = useState(false);
  const [agreed, setAgreed] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSuccess, setIsSuccess] = useState(false);

  useEffect(() => {
    document.title = 'Contact DevBotics | App & Web Development Agency';
    const meta = document.querySelector('meta[name="description"]');
    if (meta) meta.setAttribute('content', 'Get in touch with DevBotics. We respond to every inquiry within 2 business hours. No sales pitches, just honest conversations.');
  }, []);

  const handleCopy = (text: string, e: React.MouseEvent) => {
    navigator.clipboard.writeText(text);
    const span = e.currentTarget.querySelector('.copy-tag') as HTMLElement;
    if (span) { span.innerText = 'Copied!'; setTimeout(() => span.innerText = 'Copy', 2000); }
  };

  const submitForm = () => {
    setIsSubmitting(true);
    // TODO: Integrate EmailJS or actual API call here
    setTimeout(() => {
      setIsSubmitting(false);
      setIsSuccess(true);
    }, 1500);
  };

  const step1Valid = formData.name.length > 2 && /^\S+@\S+\.\S+$/.test(formData.email);
  const step2Valid = formData.services.length > 0;
  const step3Valid = formData.budget && formData.timeline && formData.desc.length > 10;
  const step4Valid = captchaValid && agreed;

  return (
    <main style={{ background: '#FFFFFF', minHeight: '100vh', paddingTop: 72 }}>
      {/* SECTION 1 — PAGE OPENER */}
      <section style={{ padding: '80px 40px 40px', position: 'relative', maxWidth: 1400, margin: '0 auto' }}>
        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#9CA3AF', marginBottom: 60 }}>
          <Link to="/" style={{ color: 'inherit', textDecoration: 'none' }}>Home</Link> / <span style={{ color: '#111827' }}>Contact</span>
        </div>
        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 16 }}>// GET IN TOUCH</div>
        <h1 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 'clamp(48px,7vw,80px)', color: '#111827', lineHeight: 0.95, margin: '0 0 40px' }}>
          Let's Build<br />Something <span style={{ color: '#1F8844' }}>Great.</span>
        </h1>
        <div style={{ height: 1, background: '#E5E7EB', width: '100%', marginBottom: 32 }} />
        <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 18, color: '#4B5563', maxWidth: 500, lineHeight: 1.6, margin: 0 }}>
          We respond to every inquiry within 2 business hours. No sales pitches. Just honest conversations.
        </p>
        <div style={{ position: 'absolute', top: 180, right: 40, display: 'flex', flexDirection: 'column', gap: 8 }} className="hide-mobile">
          <LiveClock tz="Asia/Kolkata" label="INDIA" />
          <LiveClock tz="America/New_York" label="USA" />
        </div>
      </section>

      {/* SECTION 2 — MAIN CONTACT SPLIT */}
      <section style={{ maxWidth: 1400, margin: '0 auto', display: 'flex', borderTop: '1px solid #E5E7EB' }} className="contact-split">
        {/* LEFT PANEL */}
        <div style={{ flex: 1, padding: '60px 40px', borderRight: '1px solid #E5E7EB' }} className="contact-left">
          {/* Methods */}
          <div style={{ display: 'flex', flexDirection: 'column' }}>
            <div style={{ paddingBottom: 24, borderBottom: '1px solid #E5E7EB', cursor: 'pointer', position: 'relative' }} onClick={e => handleCopy('hello@devbotics.in', e)} className="hover-underline-parent">
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#4B5563', letterSpacing: '0.15em', marginBottom: 8 }}>EMAIL US</div>
              <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 22, color: '#111827' }} className="hover-underline">hello@devbotics.in</div>
              <span className="copy-tag" style={{ position: 'absolute', right: 0, top: 32, fontSize: 11, color: '#1F8844', opacity: 0, transition: 'opacity 0.2s', fontFamily: 'Lexend,sans-serif' }}>Copy</span>
            </div>
            <a href="https://wa.me/919876543210" target="_blank" rel="noopener noreferrer" style={{ textDecoration: 'none', padding: '24px 0', borderBottom: '1px solid #E5E7EB', position: 'relative', display: 'block' }} className="hover-underline-parent">
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#4B5563', letterSpacing: '0.15em', marginBottom: 8 }}>CALL / WHATSAPP</div>
              <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 22, color: '#111827' }} className="hover-underline">+91 98765 43210</div>
            </a>
            <div style={{ padding: '24px 0', borderBottom: '1px solid #E5E7EB' }}>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#4B5563', letterSpacing: '0.15em', marginBottom: 8 }}>RESPONSE TIME</div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <span style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 22, color: '#111827' }}>Under 2 Hours</span>
                <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563' }}>Mon–Sat, 9AM–7PM IST</span>
                <LiveClock tz="Asia/Kolkata" label="" />
              </div>
            </div>
          </div>

          {/* Offices */}
          <div style={{ marginTop: 60 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 24 }}>// OUR OFFICES</div>
            <div style={{ marginBottom: 32 }}>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#4B5563', marginBottom: 8 }}>🇮🇳 INDIA · HQ</div>
              <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 20, color: '#111827', marginBottom: 12 }}>Jaipur, Rajasthan</div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563', lineHeight: 1.6, marginBottom: 12 }}>Plot No. XX, Malviya Nagar,<br />Jaipur - 302017, India</div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', marginBottom: 16 }}>+91 98765 43210<br />jaipur@devbotics.in</div>
              <a href="https://maps.google.com" target="_blank" rel="noopener noreferrer" style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#9CA3AF', textDecoration: 'none', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#1F8844'} onMouseLeave={e => e.currentTarget.style.color = '#9CA3AF'}>View on Maps →</a>
            </div>
            <div style={{ height: 1, background: '#E5E7EB', marginBottom: 32 }} />
            <div style={{ marginBottom: 32 }}>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#4B5563', marginBottom: 8 }}>🇺🇸 USA · SALES</div>
              <div style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 20, color: '#111827', marginBottom: 12 }}>Delaware, United States</div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563', lineHeight: 1.6, marginBottom: 12 }}>8 The Green, Suite A,<br />Dover, Delaware - 19901, USA</div>
              <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', marginBottom: 16 }}>+1 (302) 555-0198<br />usa@devbotics.com</div>
              <a href="https://maps.google.com" target="_blank" rel="noopener noreferrer" style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#9CA3AF', textDecoration: 'none', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#1F8844'} onMouseLeave={e => e.currentTarget.style.color = '#9CA3AF'}>View on Maps →</a>
            </div>
            <div style={{ height: 1, background: '#E5E7EB', marginBottom: 32 }} />
          </div>

          {/* Social */}
          <div style={{ marginTop: 40 }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#1F8844', letterSpacing: '0.2em', marginBottom: 16 }}>// FIND US ON</div>
            <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap', fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#9CA3AF' }}>
              {['LinkedIn', 'Instagram', 'Twitter/X', 'YouTube', 'GitHub', 'WhatsApp'].map((s, i, arr) => (
                <span key={s} style={{ display: 'flex', gap: 16 }}>
                  <a href="#" style={{ color: 'inherit', textDecoration: 'none', transition: 'color 0.2s' }} className="social-link" onMouseEnter={e => e.currentTarget.style.color = '#111827'} onMouseLeave={e => e.currentTarget.style.color = 'inherit'}>{s} <span className="arrow" style={{ opacity: 0, color: '#1F8844', transition: 'opacity 0.2s' }}>↗</span></a>
                  {i < arr.length - 1 && <span>·</span>}
                </span>
              ))}
            </div>
          </div>

          {/* Map Embed */}
          <div style={{ marginTop: 60 }}>
            <div style={{ display: 'flex', gap: 8, marginBottom: 12 }}>
              <button style={{ background: '#1F8844', color: '#FFFFFF', border: 'none', padding: '6px 12px', borderRadius: 4, fontFamily: 'Lexend,sans-serif', fontSize: 12, cursor: 'pointer' }}>🇮🇳 India</button>
              <button style={{ background: '#F9FAFB', color: '#4B5563', border: 'none', padding: '6px 12px', borderRadius: 4, fontFamily: 'Lexend,sans-serif', fontSize: 12, cursor: 'pointer' }}>🇺🇸 USA</button>
            </div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3559.877283914113!2d75.8068598150434!3d26.84385508315732!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396db66d84f8dbbd%3A0xe54df612809fb0fb!2sMalviya%20Nagar%2C%20Jaipur%2C%20Rajasthan!5e0!3m2!1sen!2sin!4v1625580000000!5m2!1sen!2sin" width="100%" height="260" style={{ border: '1px solid #E5E7EB', borderRadius: 8, filter: 'grayscale(100%)', transition: 'filter 0.3s' }} allowFullScreen loading="lazy" onMouseEnter={e => (e.currentTarget.style.filter = 'grayscale(60%)')} onMouseLeave={e => (e.currentTarget.style.filter = 'grayscale(100%)')} />
          </div>
        </div>

        {/* RIGHT PANEL - FORM */}
        <div style={{ flex: 1, position: 'relative' }} className="contact-right">
          <div style={{ position: 'sticky', top: 80, padding: 48, background: '#0A0C0F', minHeight: 'calc(100vh - 80px)', display: 'flex', flexDirection: 'column' }}>
            {!isSuccess ? (
              <>
                <div style={{ marginBottom: 40 }}>
                  <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#4B5563', letterSpacing: '0.15em', marginBottom: 12 }}>STEP 0{step} OF 04</div>
                  <div style={{ width: '100%', height: 2, background: '#E5E7EB' }}>
                    <div style={{ width: `${step * 25}%`, height: '100%', background: '#1F8844', transition: 'width 0.4s ease' }} />
                  </div>
                </div>

                <div style={{ flex: 1, position: 'relative', overflow: 'hidden' }}>
                  {/* STEP 1 */}
                  {step === 1 && (
                    <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                      <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 32, color: '#111827', marginBottom: 40 }}>First, tell us<br />about yourself.</h2>
                      <div style={{ display: 'flex', flexDirection: 'column', gap: 32 }}>
                        <div className="input-group">
                          <input type="text" value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} required />
                          <label>Full Name</label>
                        </div>
                        <div className="input-group">
                          <input type="email" value={formData.email} onChange={e => setFormData({ ...formData, email: e.target.value })} required />
                          <label>Email Address</label>
                          {/^\S+@\S+\.\S+$/.test(formData.email) && <span style={{ position: 'absolute', right: 0, top: 12, color: '#1F8844' }}>✓</span>}
                        </div>
                        <div className="input-group" style={{ display: 'flex' }}>
                          <select style={{ background: 'transparent', border: 'none', borderBottom: '1px solid #E5E7EB', color: '#111827', outline: 'none', paddingRight: 10, fontFamily: 'Lexend,sans-serif', fontSize: 16 }}>
                            <option>+91</option><option>+1</option><option>+44</option>
                          </select>
                          <input type="tel" value={formData.phone} onChange={e => setFormData({ ...formData, phone: e.target.value })} required style={{ flex: 1, marginLeft: 16 }} />
                          <label style={{ left: 60 }}>Phone Number</label>
                        </div>
                      </div>
                      <button onClick={() => setStep(2)} disabled={!step1Valid} style={{ width: '100%', padding: '16px 0', background: '#111827', color: '#FFFFFF', border: 'none', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, cursor: step1Valid ? 'pointer' : 'not-allowed', opacity: step1Valid ? 1 : 0.3, marginTop: 48, transition: 'all 0.2s' }} onMouseEnter={e => step1Valid && (e.currentTarget.style.background = '#1F8844')} onMouseLeave={e => step1Valid && (e.currentTarget.style.background = '#111827')}>
                        Continue →
                      </button>
                    </div>
                  )}

                  {/* STEP 2 */}
                  {step === 2 && (
                    <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                      <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 32, color: '#111827', marginBottom: 40 }}>What are you<br />looking to build?</h2>
                      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                        {['App Development', 'Web Development', 'AI & Automation', 'Software Development', 'Digital Marketing', 'Hire Developers', 'UI/UX Design', 'Just a Consultation'].map(srv => {
                          const isSel = formData.services.includes(srv);
                          return (
                            <button key={srv} onClick={() => {
                              const arr = isSel ? formData.services.filter(s => s !== srv) : [...formData.services, srv];
                              setFormData({ ...formData, services: arr });
                            }} style={{
                              background: isSel ? 'rgba(232,255,71,0.06)' : '#F9FAFB', border: `1px solid ${isSel ? '#1F8844' : '#E5E7EB'}`,
                              borderRadius: 6, padding: '16px', textAlign: 'left', fontFamily: 'Lexend,sans-serif', fontWeight: 500, fontSize: 14,
                              color: isSel ? '#111827' : '#4B5563', cursor: 'pointer', position: 'relative', transition: 'all 0.2s'
                            }}>
                              {srv}
                              {isSel && <span style={{ position: 'absolute', top: 16, right: 16, color: '#1F8844', fontSize: 12 }}>✓</span>}
                            </button>
                          );
                        })}
                      </div>
                      <div style={{ display: 'flex', gap: 24, marginTop: 48, alignItems: 'center' }}>
                        <button onClick={() => setStep(1)} style={{ background: 'transparent', border: 'none', color: '#4B5563', fontFamily: 'Lexend,sans-serif', fontSize: 14, cursor: 'pointer' }} onMouseEnter={e => e.currentTarget.style.color = '#111827'} onMouseLeave={e => e.currentTarget.style.color = '#4B5563'}>← Back</button>
                        <button onClick={() => setStep(3)} disabled={!step2Valid} style={{ flex: 1, padding: '16px 0', background: '#111827', color: '#FFFFFF', border: 'none', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, cursor: step2Valid ? 'pointer' : 'not-allowed', opacity: step2Valid ? 1 : 0.3, transition: 'all 0.2s' }} onMouseEnter={e => step2Valid && (e.currentTarget.style.background = '#1F8844')} onMouseLeave={e => step2Valid && (e.currentTarget.style.background = '#111827')}>
                          Continue →
                        </button>
                      </div>
                    </div>
                  )}

                  {/* STEP 3 */}
                  {step === 3 && (
                    <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                      <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 32, color: '#111827', marginBottom: 40 }}>Tell us more<br />about your project.</h2>
                      <div style={{ marginBottom: 24 }}>
                        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#4B5563', marginBottom: 12 }}>Budget Range</div>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                          {['Under ₹1L', '₹1–3L', '₹3–5L', '₹5L+', 'Custom'].map(b => (
                            <button key={b} onClick={() => setFormData({ ...formData, budget: b })} style={{ background: formData.budget === b ? 'rgba(232,255,71,0.06)' : '#F9FAFB', border: `1px solid ${formData.budget === b ? '#1F8844' : '#E5E7EB'}`, borderRadius: 4, padding: '10px 16px', color: formData.budget === b ? '#111827' : '#4B5563', fontFamily: 'Lexend,sans-serif', fontSize: 13, cursor: 'pointer', transition: 'all 0.2s' }}>{b}</button>
                          ))}
                        </div>
                      </div>
                      <div style={{ marginBottom: 32 }}>
                        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 12, color: '#4B5563', marginBottom: 12 }}>Timeline</div>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                          {['ASAP', '1–3 Months', '3–6 Months', 'Flexible'].map(t => (
                            <button key={t} onClick={() => setFormData({ ...formData, timeline: t })} style={{ background: formData.timeline === t ? 'rgba(232,255,71,0.06)' : '#F9FAFB', border: `1px solid ${formData.timeline === t ? '#1F8844' : '#E5E7EB'}`, borderRadius: 4, padding: '10px 16px', color: formData.timeline === t ? '#111827' : '#4B5563', fontFamily: 'Lexend,sans-serif', fontSize: 13, cursor: 'pointer', transition: 'all 0.2s' }}>{t}</button>
                          ))}
                        </div>
                      </div>
                      <div className="input-group" style={{ marginBottom: 32 }}>
                        <textarea value={formData.desc} onChange={e => setFormData({ ...formData, desc: e.target.value })} required rows={4} maxLength={500} style={{ resize: 'none' }} />
                        <label>Project Description</label>
                        <span style={{ position: 'absolute', bottom: 10, right: 0, fontFamily: 'Lexend,sans-serif', fontSize: 11, color: '#9CA3AF' }}>{formData.desc.length} / 500</span>
                      </div>
                      <div style={{ border: '1px dashed #E5E7EB', borderRadius: 8, padding: 32, textAlign: 'center', cursor: 'pointer', transition: 'border-color 0.2s' }} onMouseEnter={e => e.currentTarget.style.borderColor = '#1F8844'} onMouseLeave={e => e.currentTarget.style.borderColor = '#E5E7EB'}>
                        <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#9CA3AF' }}>Drop your brief, wireframe,<br />or reference files here</div>
                      </div>
                      <div style={{ display: 'flex', gap: 24, marginTop: 48, alignItems: 'center' }}>
                        <button onClick={() => setStep(2)} style={{ background: 'transparent', border: 'none', color: '#4B5563', fontFamily: 'Lexend,sans-serif', fontSize: 14, cursor: 'pointer' }} onMouseEnter={e => e.currentTarget.style.color = '#111827'} onMouseLeave={e => e.currentTarget.style.color = '#4B5563'}>← Back</button>
                        <button onClick={() => setStep(4)} disabled={!step3Valid} style={{ flex: 1, padding: '16px 0', background: '#111827', color: '#FFFFFF', border: 'none', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, cursor: step3Valid ? 'pointer' : 'not-allowed', opacity: step3Valid ? 1 : 0.3, transition: 'all 0.2s' }} onMouseEnter={e => step3Valid && (e.currentTarget.style.background = '#1F8844')} onMouseLeave={e => step3Valid && (e.currentTarget.style.background = '#111827')}>
                          Continue →
                        </button>
                      </div>
                    </div>
                  )}

                  {/* STEP 4 */}
                  {step === 4 && (
                    <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                      <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 32, color: '#111827', marginBottom: 40 }}>Almost there.<br />Verify you're human.</h2>
                      
                      <MathCaptcha onValid={setCaptchaValid} />

                      <div style={{ background: '#F9FAFB', border: '1px solid #E5E7EB', padding: 20, borderRadius: 8, marginTop: 32, marginBottom: 24, fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#9CA3AF', display: 'flex', flexDirection: 'column', gap: 8 }}>
                        <div>Sending as: <span style={{ color: '#111827' }}>{formData.name}</span></div>
                        <div>Service: <span style={{ color: '#111827' }}>{formData.services.join(', ')}</span></div>
                        <div>Budget: <span style={{ color: '#111827' }}>{formData.budget}</span></div>
                      </div>

                      <div style={{ display: 'flex', alignItems: 'flex-start', gap: 12, marginBottom: 32, cursor: 'pointer' }} onClick={() => setAgreed(!agreed)}>
                        <div style={{ width: 16, height: 16, border: '1px solid #E5E7EB', background: agreed ? '#1F8844' : 'transparent', marginTop: 2, flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                          {agreed && <svg width="10" height="8" viewBox="0 0 10 8" fill="none" stroke="#FFFFFF" strokeWidth="2"><path d="M1 4l2.5 2.5L9 1"/></svg>}
                        </div>
                        <span style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', lineHeight: 1.5 }}>I agree to the Privacy Policy and Terms of Service</span>
                      </div>

                      <div style={{ display: 'flex', gap: 24, alignItems: 'center' }}>
                        <button onClick={() => setStep(3)} style={{ background: 'transparent', border: 'none', color: '#4B5563', fontFamily: 'Lexend,sans-serif', fontSize: 14, cursor: 'pointer' }} onMouseEnter={e => e.currentTarget.style.color = '#111827'} onMouseLeave={e => e.currentTarget.style.color = '#4B5563'}>← Back</button>
                        <button onClick={submitForm} disabled={!step4Valid || isSubmitting} style={{ flex: 1, padding: '16px 0', background: '#111827', color: '#FFFFFF', border: 'none', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 15, cursor: step4Valid && !isSubmitting ? 'pointer' : 'not-allowed', opacity: step4Valid && !isSubmitting ? 1 : 0.3, transition: 'all 0.2s', display: 'flex', justifyContent: 'center', gap: 8 }} onMouseEnter={e => step4Valid && !isSubmitting && (e.currentTarget.style.background = '#1F8844')} onMouseLeave={e => step4Valid && !isSubmitting && (e.currentTarget.style.background = '#111827')}>
                          {isSubmitting ? <>Sending <span className="loading-dots"></span></> : 'Send My Project Brief →'}
                        </button>
                      </div>
                    </div>
                  )}
                </div>
              </>
            ) : (
              /* SUCCESS STATE */
              <div style={{ animation: 'slideInRight 0.4s ease forwards', display: 'flex', flexDirection: 'column', justifyContent: 'center', height: '100%', paddingBottom: 60 }}>
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" style={{ marginBottom: 32 }}>
                  <circle cx="32" cy="32" r="30" stroke="#1F8844" strokeWidth="4" strokeDasharray="188" strokeDashoffset="188" style={{ animation: 'drawCircle 0.6s ease forwards' }} />
                  <path d="M18 32l10 10 18-18" stroke="#1F8844" strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" strokeDasharray="50" strokeDashoffset="50" style={{ animation: 'drawCheck 0.4s ease 0.6s forwards' }} />
                </svg>
                <h2 style={{ fontFamily: 'Montserrat,sans-serif', fontWeight: 900, fontSize: 36, color: '#111827', marginBottom: 16 }}>Message Received.</h2>
                <p style={{ fontFamily: 'Lexend,sans-serif', fontSize: 16, color: '#4B5563', lineHeight: 1.6, marginBottom: 32 }}>We'll reach out to you at <strong style={{ color: '#111827', fontWeight: 400 }}>{formData.email}</strong> within 2 hours.</p>
                <div style={{ fontFamily: 'Lexend, monospace', fontSize: 12, color: '#9CA3AF', marginBottom: 48 }}>Your reference: #DBT-{Date.now().toString().slice(-6)}</div>
                <div style={{ display: 'flex', gap: 24 }}>
                  <Link to="/" style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563', textDecoration: 'none', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#1F8844'} onMouseLeave={e => e.currentTarget.style.color = '#4B5563'}>← Back to Home</Link>
                  <Link to="/" style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#4B5563', textDecoration: 'none', transition: 'color 0.2s' }} onMouseEnter={e => e.currentTarget.style.color = '#1F8844'} onMouseLeave={e => e.currentTarget.style.color = '#4B5563'}>View Services</Link>
                </div>
              </div>
            )}
          </div>
        </div>
      </section>

      {/* SECTION 3 — QUICK CONNECT BAR */}
      <section style={{ background: '#F9FAFB', borderTop: '1px solid #E5E7EB' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', display: 'grid', gridTemplateColumns: 'repeat(3,1fr)' }} className="quick-connect">
          <div style={{ padding: '32px 0', textAlign: 'center', borderRight: '1px solid #E5E7EB' }} className="hover-underline-parent">
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#111827', marginBottom: 8 }}>📧 Drop an Email</div>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563', cursor: 'pointer' }} onClick={e => handleCopy('hello@devbotics.in', e)}>
              <span className="hover-underline">hello@devbotics.in</span> <span className="copy-tag" style={{ color: '#1F8844', opacity: 0, transition: 'opacity 0.2s' }}>Copy →</span>
            </div>
          </div>
          <div style={{ padding: '32px 0', textAlign: 'center', borderRight: '1px solid #E5E7EB' }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#111827', marginBottom: 8 }}>💬 WhatsApp Us</div>
            <a href="https://wa.me/919876543210" target="_blank" rel="noopener noreferrer" style={{ display: 'inline-block', background: '#1F8844', color: '#FFFFFF', fontFamily: 'Montserrat,sans-serif', fontWeight: 600, fontSize: 13, padding: '6px 16px', textDecoration: 'none' }}>Start chat →</a>
          </div>
          <div style={{ padding: '32px 0', textAlign: 'center' }}>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 14, color: '#111827', marginBottom: 8 }}>📅 Book a Call</div>
            <div style={{ fontFamily: 'Lexend,sans-serif', fontSize: 13, color: '#4B5563' }}>
              Schedule 30-min free consultation <br/><a href="mailto:hello@devbotics.in?subject=Book%20a%20Call" style={{ color: '#1F8844', textDecoration: 'none' }}>Pick a slot →</a>
            </div>
          </div>
        </div>
      </section>

      {/* SECTION 4 — TRUST SIGNALS */}
      <section style={{ background: '#FFFFFF', borderTop: '1px solid #E5E7EB' }}>
        <div style={{ maxWidth: 1400, margin: '0 auto', display: 'grid', gridTemplateColumns: 'repeat(4,1fr)' }} className="trust-signals">
          {['🔒 NDA Before Every Call', '⚡ 2-Hour Response', '✅ Free Consultation', '🌍 India + USA Offices'].map((t, i) => (
            <div key={t} style={{ padding: '20px 0', textAlign: 'center', borderRight: i < 3 ? '1px solid #E5E7EB' : 'none', fontFamily: 'Lexend,sans-serif', fontWeight: 300, fontSize: 13, color: '#9CA3AF', transition: 'color 0.2s', cursor: 'default' }} onMouseEnter={e => e.currentTarget.style.color = '#4B5563'} onMouseLeave={e => e.currentTarget.style.color = '#9CA3AF'}>
              {t}
            </div>
          ))}
        </div>
      </section>

      <style>{`
        .input-group { position: relative; }
        .input-group input, .input-group textarea {
          width: 100%; background: transparent; border: none; border-bottom: 1px solid #E5E7EB;
          padding: 12px 0; fontFamily: Lexend, sans-serif; font-size: 16px; color: #111827; outline: none; transition: border-color 0.2s;
        }
        .input-group input:focus, .input-group textarea:focus { border-bottom-color: #1F8844; }
        .input-group label {
          position: absolute; left: 0; top: 12px; fontFamily: Lexend, sans-serif; font-size: 16px; color: #4B5563;
          transition: all 0.2s ease; pointer-events: none;
        }
        .input-group input:focus ~ label, .input-group input:valid ~ label,
        .input-group textarea:focus ~ label, .input-group textarea:valid ~ label {
          top: -16px; font-size: 12px; color: #1F8844;
        }
        .hover-underline-parent:hover .copy-tag, .hover-underline-parent:hover .arrow { opacity: 1 !important; }
        .hover-underline-parent:hover .hover-underline { color: #1F8844 !important; text-decoration: underline; text-underline-offset: 4px; }
        @keyframes slideUpFade { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes drawCircle { to { stroke-dashoffset: 0; } }
        @keyframes drawCheck { to { stroke-dashoffset: 0; } }
        @keyframes loadingDots { 0% { content: "."; } 33% { content: ".."; } 66% { content: "..."; } }
        .loading-dots::after { content: "."; animation: loadingDots 1.5s infinite steps(1); display: inline-block; width: 12px; text-align: left; }
        @media(max-width:1024px) {
          .contact-split { flex-direction: column; }
          .contact-left { border-right: none; border-bottom: 1px solid #E5E7EB; }
          .contact-right > div { position: static !important; min-height: auto !important; }
          .hide-mobile { display: none !important; }
        }
        @media(max-width:768px) {
          .quick-connect { grid-template-columns: 1fr !important; }
          .quick-connect > div { border-right: none !important; border-bottom: 1px solid #E5E7EB; }
          .trust-signals { grid-template-columns: repeat(2,1fr) !important; }
          .trust-signals > div:nth-child(even) { border-right: none !important; }
          .trust-signals > div:nth-child(1), .trust-signals > div:nth-child(2) { border-bottom: 1px solid #E5E7EB; }
        }
      `}</style>
    </main>
  );
}
