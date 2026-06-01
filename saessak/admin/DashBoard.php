import React, { useState } from 'react';
import { 
  Users, Calendar, CheckCircle, AlertCircle, 
  Search, Bell, UserPlus, Megaphone, Activity
} from 'lucide-react';
import { 
  LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer 
} from 'recharts';

// --- [Mock Data] ---
const chartData = [
  { time: '09:00', patients: 12 },
  { time: '10:00', patients: 25 },
  { time: '11:00', patients: 38 },
  { time: '12:00', patients: 20 },
  { time: '13:00', patients: 15 },
  { time: '14:00', patients: 30 },
  { time: '15:00', patients: 45 },
  { time: '16:00', patients: 35 },
];

const waitlistData = [
  { id: '1001', name: '김철수', time: '10:30', dept: '내과', status: '진료중', note: '혈압 체크 필요' },
  { id: '1002', name: '이영희', time: '10:45', dept: '이비인후과', status: '대기중', note: '초진' },
  { id: '1003', name: '박지민', time: '11:00', dept: '정형외과', status: '대기중', note: 'X-Ray 촬영 완료' },
  { id: '1004', name: '최동훈', time: '10:15', dept: '내과', status: '완료', note: '처방전 발급' },
];

// --- [Status Color Helper] ---
const getStatusColor = (status) => {
  switch (status) {
    case '진료중': return 'bg-blue-100 text-blue-700 border-blue-200';
    case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
    case '완료': return 'bg-gray-100 text-gray-700 border-gray-200';
    default: return 'bg-gray-100 text-gray-700';
  }
};

export default function AdminDashboard() {
  return (
    <div className="flex h-screen bg-gray-50 text-gray-900 font-sans">
      
      {/* 1. LNB (좌측 메뉴바) */}
      <aside className="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex">
        <div className="p-6 border-b border-gray-200">
          <h1 className="text-2xl font-bold text-blue-600 tracking-tight">MediAdmin</h1>
        </div>
        <nav className="flex-1 p-4 space-y-2">
          <a href="#" className="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg font-medium">
            <Activity className="w-5 h-5" /> 대시보드
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <Users className="w-5 h-5" /> 환자 관리
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <Calendar className="w-5 h-5" /> 예약 캘린더
          </a>
        </nav>
      </aside>

      {/* 2. 메인 콘텐츠 영역 */}
      <main className="flex-1 flex flex-col overflow-hidden">
        
        {/* 상단 헤더 */}
        <header className="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0">
          <div className="relative w-96">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input 
              type="text" 
              placeholder="환자 이름, 연락처 검색 (단축키: /)" 
              className="w-full pl-10 pr-4 py-2 bg-gray-100 border-transparent rounded-md focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none"
            />
          </div>
          <div className="flex items-center gap-4">
            <button className="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
              <Bell className="w-6 h-6" />
              <span className="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <div className="flex items-center gap-2 pl-4 border-l border-gray-200">
              <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">A</div>
              <span className="font-medium text-sm">관리자님</span>
            </div>
          </div>
        </header>

        {/* 대시보드 콘텐츠 스크롤 영역 */}
        <div className="flex-1 overflow-auto p-8 space-y-6">
          
          {/* 상단 KPI 요약 카드 */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
              <div className="p-4 bg-blue-100 text-blue-600 rounded-lg"><Calendar className="w-8 h-8" /></div>
              <div>
                <p className="text-sm text-gray-500 font-medium">금일 전체 예약</p>
                <p className="text-2xl font-bold">142<span className="text-sm font-normal text-gray-400 ml-1">명</span></p>
              </div>
            </div>
            <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
              <div className="p-4 bg-yellow-100 text-yellow-600 rounded-lg"><Users className="w-8 h-8" /></div>
              <div>
                <p className="text-sm text-gray-500 font-medium">현재 대기 환자</p>
                <p className="text-2xl font-bold text-yellow-600">18<span className="text-sm font-normal text-gray-400 ml-1">명</span></p>
              </div>
            </div>
            <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
              <div className="p-4 bg-green-100 text-green-600 rounded-lg"><CheckCircle className="w-8 h-8" /></div>
              <div>
                <p className="text-sm text-gray-500 font-medium">진료 완료</p>
                <p className="text-2xl font-bold">85<span className="text-sm font-normal text-gray-400 ml-1">명</span></p>
              </div>
            </div>
            <div className="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
              <div className="p-4 bg-red-100 text-red-600 rounded-lg"><AlertCircle className="w-8 h-8" /></div>
              <div>
                <p className="text-sm text-gray-500 font-medium">긴급 알림</p>
                <p className="text-2xl font-bold text-red-600">2<span className="text-sm font-normal text-gray-400 ml-1">건</span></p>
              </div>
            </div>
          </div>

          {/* 메인 레이아웃 (좌: 대기현황, 우: 빠른액션/그래프) */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {/* 좌측: 실시간 대기 현황표 (2칸 차지) */}
            <div className="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
              <div className="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h2 className="text-lg font-bold text-gray-800">실시간 대기 현황</h2>
                <button className="text-sm text-blue-600 font-medium hover:underline">전체보기</button>
              </div>
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-gray-50 text-gray-500 text-sm border-b border-gray-200">
                      <th className="px-6 py-4 font-medium">예약시간</th>
                      <th className="px-6 py-4 font-medium">환자명 (차트번호)</th>
                      <th className="px-6 py-4 font-medium">진료과</th>
                      <th className="px-6 py-4 font-medium">상태</th>
                      <th className="px-6 py-4 font-medium">비고</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200 text-sm">
                    {waitlistData.map((row) => (
                      <tr key={row.id} className="hover:bg-gray-50 transition-colors">
                        <td className="px-6 py-4 font-medium text-gray-900">{row.time}</td>
                        <td className="px-6 py-4">
                          <span className="font-bold">{row.name}</span> <span className="text-gray-400">({row.id})</span>
                        </td>
                        <td className="px-6 py-4 text-gray-600">{row.dept}</td>
                        <td className="px-6 py-4">
                          <span className={`px-3 py-1 rounded-full text-xs font-bold border ${getStatusColor(row.status)}`}>
                            {row.status}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-gray-500 truncate max-w-xs">{row.note}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            {/* 우측: 퀵 액션 & 차트 (1칸 차지) */}
            <div className="space-y-6">
              
              {/* 간편 행동 (Quick Actions) */}
              <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 className="text-lg font-bold text-gray-800 mb-4">빠른 실행</h2>
                <div className="grid grid-cols-2 gap-3">
                  <button className="flex flex-col items-center justify-center p-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors gap-2">
                    <UserPlus className="w-6 h-6" />
                    <span className="font-medium text-sm">현장 접수</span>
                  </button>
                  <button className="flex flex-col items-center justify-center p-4 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors gap-2">
                    <Megaphone className="w-6 h-6" />
                    <span className="font-medium text-sm">긴급 공지</span>
                  </button>
                </div>
              </div>

              {/* 통계 그래프 (Recharts 사용) */}
              <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 className="text-lg font-bold text-gray-800 mb-4">시간대별 내원 추이</h2>
                <div className="h-60 w-full">
                  <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={chartData}>
                      <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#E5E7EB" />
                      <XAxis dataKey="time" axisLine={false} tickLine={false} tick={{ fontSize: 12, fill: '#6B7280' }} dy={10} />
                      <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 12, fill: '#6B7280' }} dx={-10} />
                      <Tooltip 
                        contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                      />
                      <Line 
                        type="monotone" 
                        dataKey="patients" 
                        stroke="#2563EB" 
                        strokeWidth={3} 
                        dot={{ r: 4, fill: '#2563EB', strokeWidth: 2, stroke: '#fff' }} 
                        activeDot={{ r: 6 }}
                      />
                    </LineChart>
                  </ResponsiveContainer>
                </div>
              </div>

            </div>
          </div>

        </div>
      </main>
    </div>
  );
}