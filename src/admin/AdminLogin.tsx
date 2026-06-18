import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function AdminLogin() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [showPass, setShowPass] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      await login(email, password);
      navigate('/admin/dashboard');
    } catch {
      setError('Invalid credentials. Please check your email and password.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{
      minHeight: '100vh', background: '#0A0C0F',
      display: 'flex', alignItems: 'center', justifyContent: 'center',
      fontFamily: 'Lexend, sans-serif', padding: '24px',
    }}>
      {/* Background grid pattern */}
      <div style={{
        position: 'fixed', inset: 0, opacity: 0.04,
        backgroundImage: 'linear-gradient(#1F8844 1px, transparent 1px), linear-gradient(90deg, #1F8844 1px, transparent 1px)',
        backgroundSize: '40px 40px', pointerEvents: 'none',
      }} />

      <div style={{
        width: '100%', maxWidth: 420, position: 'relative', zIndex: 1,
      }}>
        {/* Logo */}
        <div style={{ textAlign: 'center', marginBottom: 48 }}>
          <span style={{
            fontFamily: 'Montserrat, sans-serif', fontWeight: 900, fontSize: 28,
            color: '#FFFFFF', letterSpacing: '-0.03em',
          }}>
            DevBotics<span style={{ color: '#1F8844' }}>.</span>
          </span>
          <div style={{ color: '#4B5563', fontSize: 13, marginTop: 6, letterSpacing: '0.12em', textTransform: 'uppercase' }}>
            Admin Panel
          </div>
        </div>

        {/* Card */}
        <div style={{
          background: '#111827', border: '1px solid #1F2937',
          borderRadius: 12, padding: '40px 36px',
          boxShadow: '0 32px 80px rgba(0,0,0,0.6)',
        }}>
          <h1 style={{
            fontFamily: 'Montserrat, sans-serif', fontWeight: 800, fontSize: 24,
            color: '#FFFFFF', marginBottom: 8,
          }}>Welcome back</h1>
          <p style={{ color: '#4B5563', fontSize: 14, marginBottom: 32 }}>
            Sign in to manage your website
          </p>

          {error && (
            <div style={{
              background: 'rgba(239,68,68,0.1)', border: '1px solid rgba(239,68,68,0.3)',
              borderRadius: 8, padding: '12px 16px', marginBottom: 24,
              color: '#FCA5A5', fontSize: 13,
            }}>
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
            {/* Email */}
            <div>
              <label style={{ display: 'block', color: '#9CA3AF', fontSize: 12, letterSpacing: '0.08em', marginBottom: 8, textTransform: 'uppercase' }}>
                Email Address
              </label>
              <input
                type="email" value={email} onChange={e => setEmail(e.target.value)}
                placeholder="akash@devebotics.com" required autoComplete="email"
                style={{
                  width: '100%', background: '#0A0C0F', border: '1px solid #1F2937',
                  borderRadius: 8, padding: '12px 16px', color: '#FFFFFF',
                  fontSize: 14, fontFamily: 'Lexend, sans-serif', outline: 'none',
                  transition: 'border-color 0.2s', boxSizing: 'border-box',
                }}
                onFocus={e => e.target.style.borderColor = '#1F8844'}
                onBlur={e => e.target.style.borderColor = '#1F2937'}
              />
            </div>

            {/* Password */}
            <div>
              <label style={{ display: 'block', color: '#9CA3AF', fontSize: 12, letterSpacing: '0.08em', marginBottom: 8, textTransform: 'uppercase' }}>
                Password
              </label>
              <div style={{ position: 'relative' }}>
                <input
                  type={showPass ? 'text' : 'password'} value={password}
                  onChange={e => setPassword(e.target.value)}
                  placeholder="••••••••" required autoComplete="current-password"
                  style={{
                    width: '100%', background: '#0A0C0F', border: '1px solid #1F2937',
                    borderRadius: 8, padding: '12px 44px 12px 16px', color: '#FFFFFF',
                    fontSize: 14, fontFamily: 'Lexend, sans-serif', outline: 'none',
                    transition: 'border-color 0.2s', boxSizing: 'border-box',
                  }}
                  onFocus={e => e.target.style.borderColor = '#1F8844'}
                  onBlur={e => e.target.style.borderColor = '#1F2937'}
                />
                <button type="button" onClick={() => setShowPass(!showPass)} style={{
                  position: 'absolute', right: 12, top: '50%', transform: 'translateY(-50%)',
                  background: 'transparent', border: 'none', color: '#4B5563', cursor: 'pointer',
                  padding: 4, display: 'flex', alignItems: 'center',
                }}>
                  {showPass
                    ? <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    : <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  }
                </button>
              </div>
            </div>

            {/* Submit */}
            <button type="submit" disabled={loading} style={{
              width: '100%', padding: '14px', marginTop: 8,
              background: loading ? '#1F2937' : '#1F8844',
              color: '#FFFFFF', border: 'none', borderRadius: 8,
              fontFamily: 'Montserrat, sans-serif', fontWeight: 700, fontSize: 15,
              cursor: loading ? 'not-allowed' : 'pointer',
              transition: 'all 0.2s', display: 'flex', alignItems: 'center',
              justifyContent: 'center', gap: 8,
            }}
              onMouseEnter={e => !loading && ((e.currentTarget as HTMLElement).style.background = '#17703a')}
              onMouseLeave={e => !loading && ((e.currentTarget as HTMLElement).style.background = '#1F8844')}
            >
              {loading ? (
                <>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" style={{ animation: 'spin 1s linear infinite' }}>
                    <path d="M21 12a9 9 0 11-6.219-8.56"/>
                  </svg>
                  Signing in...
                </>
              ) : 'Sign In →'}
            </button>
          </form>
        </div>

        <p style={{ textAlign: 'center', color: '#374151', fontSize: 12, marginTop: 24 }}>
          Devbotics Admin · Restricted Access
        </p>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Lexend:wght@300;400;500;600&display=swap');
        @keyframes spin { to { transform: rotate(360deg); } }
      `}</style>
    </div>
  );
}
