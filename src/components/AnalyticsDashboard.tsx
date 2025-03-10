
import React, { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { CustomProgress } from "@/components/ui/custom-progress";
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
  LineChart,
  Line,
  Legend
} from "recharts";
import { 
  ArrowUpRight, 
  ChevronDown, 
  Activity,
  Users,
  LineChart as LineChartIcon,
  BarChart as BarChartIcon,
  CheckCircle2,
  Clock
} from "lucide-react";

// Sample data (would be replaced with real data from API)
const pollActivity = [
  { name: "Mon", votes: 120 },
  { name: "Tue", votes: 220 },
  { name: "Wed", votes: 180 },
  { name: "Thu", votes: 310 },
  { name: "Fri", votes: 420 },
  { name: "Sat", votes: 380 },
  { name: "Sun", votes: 290 },
];

const pollTypeData = [
  { name: "Multiple Choice", value: 45 },
  { name: "Rating Scale", value: 20 },
  { name: "Open Ended", value: 15 },
  { name: "Binary", value: 10 },
  { name: "Quiz", value: 10 },
];

const COLORS = ['#8884d8', '#83a6ed', '#8dd1e1', '#82ca9d', '#a4de6c'];

const demographicData = [
  { age: "18-24", male: 50, female: 60, other: 15 },
  { age: "25-34", male: 80, female: 90, other: 20 },
  { age: "35-44", male: 70, female: 60, other: 10 },
  { age: "45-54", male: 40, female: 30, other: 5 },
  { age: "55+", male: 30, female: 20, other: 5 },
];

// User engagement over time
const engagementData = [
  { month: "Jan", votes: 400, comments: 240, shares: 100 },
  { month: "Feb", votes: 300, comments: 198, shares: 80 },
  { month: "Mar", votes: 500, comments: 300, shares: 150 },
  { month: "Apr", votes: 780, comments: 420, shares: 220 },
  { month: "May", votes: 600, comments: 380, shares: 170 },
  { month: "Jun", votes: 800, comments: 500, shares: 250 },
];

const AnalyticsDashboard = () => {
  const [timeRange, setTimeRange] = useState("7d");

  return (
    <div className="analytics-dashboard space-y-6">
      <div className="flex items-center justify-between">
        <h2 className="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Analytics Dashboard</h2>
        <Select value={timeRange} onValueChange={setTimeRange}>
          <SelectTrigger className="w-[180px]">
            <SelectValue placeholder="Select time range" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="24h">Last 24 hours</SelectItem>
            <SelectItem value="7d">Last 7 days</SelectItem>
            <SelectItem value="30d">Last 30 days</SelectItem>
            <SelectItem value="90d">Last 90 days</SelectItem>
            <SelectItem value="1y">Last year</SelectItem>
          </SelectContent>
        </Select>
      </div>

      {/* Key metrics */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <MetricCard 
          title="Total Polls" 
          value="256" 
          change="+12%" 
          trend="up" 
          icon={<BarChartIcon className="h-5 w-5 text-indigo-600" />} 
        />
        <MetricCard 
          title="Total Votes" 
          value="14,523" 
          change="+25%" 
          trend="up" 
          icon={<Activity className="h-5 w-5 text-emerald-600" />} 
        />
        <MetricCard 
          title="Unique Voters" 
          value="8,764" 
          change="+18%" 
          trend="up" 
          icon={<Users className="h-5 w-5 text-blue-600" />} 
        />
        <MetricCard 
          title="Completion Rate" 
          value="78%" 
          change="+5%" 
          trend="up" 
          icon={<CheckCircle2 className="h-5 w-5 text-purple-600" />} 
        />
      </div>

      {/* Charts */}
      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle>Poll Activity</CardTitle>
            <CardDescription>Votes per day over last week</CardDescription>
          </CardHeader>
          <CardContent className="pt-0">
            <div className="h-80">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={pollActivity} margin={{ top: 20, right: 30, left: 20, bottom: 5 }}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="votes" fill="#8884d8" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle>Poll Types Distribution</CardTitle>
            <CardDescription>Breakdown by poll type</CardDescription>
          </CardHeader>
          <CardContent className="pt-0">
            <div className="h-80 flex items-center justify-center">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={pollTypeData}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                    outerRadius={80}
                    fill="#8884d8"
                    dataKey="value"
                  >
                    {pollTypeData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      </div>

      <Tabs defaultValue="engagement" className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="engagement">User Engagement</TabsTrigger>
          <TabsTrigger value="demographics">Demographics</TabsTrigger>
          <TabsTrigger value="performance">Poll Performance</TabsTrigger>
        </TabsList>
        <TabsContent value="engagement">
          <Card>
            <CardHeader>
              <CardTitle>User Engagement</CardTitle>
              <CardDescription>Votes, comments and shares over time</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-80">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart
                    data={engagementData}
                    margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
                  >
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="month" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Line type="monotone" dataKey="votes" stroke="#8884d8" activeDot={{ r: 8 }} />
                    <Line type="monotone" dataKey="comments" stroke="#82ca9d" />
                    <Line type="monotone" dataKey="shares" stroke="#ffc658" />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
        <TabsContent value="demographics">
          <Card>
            <CardHeader>
              <CardTitle>Demographic Distribution</CardTitle>
              <CardDescription>Breakdown of users by age group and gender</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-80">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart
                    data={demographicData}
                    margin={{ top: 20, right: 30, left: 20, bottom: 5 }}
                  >
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="age" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Bar dataKey="male" stackId="a" fill="#8884d8" />
                    <Bar dataKey="female" stackId="a" fill="#82ca9d" />
                    <Bar dataKey="other" stackId="a" fill="#ffc658" />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
        <TabsContent value="performance">
          <Card>
            <CardHeader>
              <CardTitle>Top Performing Polls</CardTitle>
              <CardDescription>Polls with highest engagement rates</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <TopPollItem 
                  title="Which feature should we prioritize next?" 
                  votes={1245} 
                  completionRate={92} 
                  timeAgo="3 days ago"
                />
                <TopPollItem 
                  title="Do you prefer dark or light mode?" 
                  votes={986} 
                  completionRate={88} 
                  timeAgo="1 week ago"
                />
                <TopPollItem 
                  title="Rate our new product design" 
                  votes={754} 
                  completionRate={74} 
                  timeAgo="2 weeks ago"
                />
                <TopPollItem 
                  title="What's your favorite programming language?" 
                  votes={652} 
                  completionRate={81} 
                  timeAgo="3 weeks ago"
                />
                <TopPollItem 
                  title="How often do you update your software?" 
                  votes={543} 
                  completionRate={68} 
                  timeAgo="1 month ago"
                />
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

interface MetricCardProps {
  title: string;
  value: string;
  change: string;
  trend: "up" | "down";
  icon: React.ReactNode;
}

const MetricCard = ({ title, value, change, trend, icon }: MetricCardProps) => (
  <Card>
    <CardContent className="p-6">
      <div className="flex justify-between items-start">
        <div>
          <p className="text-sm font-medium text-muted-foreground">{title}</p>
          <h4 className="text-2xl font-bold mt-1">{value}</h4>
        </div>
        <div className="p-2 bg-slate-100 dark:bg-slate-800 rounded-full">
          {icon}
        </div>
      </div>
      <div className="mt-4 flex items-center">
        <div className={`text-xs font-medium ${trend === "up" ? "text-emerald-600" : "text-rose-600"}`}>
          {change}
        </div>
        <ArrowUpRight className={`h-3 w-3 ml-1 ${trend === "up" ? "text-emerald-600" : "rotate-180 text-rose-600"}`} />
        <div className="text-xs font-medium text-muted-foreground ml-2">vs last period</div>
      </div>
    </CardContent>
  </Card>
);

interface TopPollItemProps {
  title: string;
  votes: number;
  completionRate: number;
  timeAgo: string;
}

const TopPollItem = ({ title, votes, completionRate, timeAgo }: TopPollItemProps) => (
  <div className="border rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
    <div className="flex items-center justify-between mb-2">
      <h4 className="font-medium">{title}</h4>
      <div className="flex items-center text-sm text-muted-foreground">
        <Clock className="h-3 w-3 mr-1" />
        {timeAgo}
      </div>
    </div>
    <div className="grid grid-cols-2 gap-4">
      <div>
        <p className="text-sm text-muted-foreground mb-1">Total Votes</p>
        <p className="font-semibold">{votes.toLocaleString()}</p>
      </div>
      <div>
        <p className="text-sm text-muted-foreground mb-1">Completion Rate</p>
        <div className="flex items-center gap-2">
          <CustomProgress 
            value={completionRate} 
            size="sm" 
            className="flex-1" 
            fillClassName={completionRate > 80 ? "bg-emerald-500" : completionRate > 60 ? "bg-amber-500" : "bg-rose-500"}
          />
          <span className="font-semibold text-sm">{completionRate}%</span>
        </div>
      </div>
    </div>
  </div>
);

export default AnalyticsDashboard;
