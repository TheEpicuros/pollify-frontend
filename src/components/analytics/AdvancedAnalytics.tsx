
import React, { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { ResponsiveContainer, BarChart, Bar, XAxis, YAxis, Tooltip, Legend, PieChart, Pie, Cell, LineChart, Line } from "recharts";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Calendar, Clock, Users, Activity } from "lucide-react";

// Sample data - in a real app this would come from an API
const userEngagement = [
  { day: "Mon", new_users: 12, returning: 34, votes: 78 },
  { day: "Tue", new_users: 15, returning: 37, votes: 92 },
  { day: "Wed", new_users: 18, returning: 42, votes: 105 },
  { day: "Thu", new_users: 22, returning: 45, votes: 118 },
  { day: "Fri", new_users: 25, returning: 48, votes: 132 },
  { day: "Sat", new_users: 30, returning: 52, votes: 145 },
  { day: "Sun", new_users: 28, returning: 47, votes: 128 },
];

const deviceData = [
  { name: "Desktop", value: 58 },
  { name: "Mobile", value: 34 },
  { name: "Tablet", value: 8 },
];

const trafficSourceData = [
  { name: "Direct", value: 42 },
  { name: "Social Media", value: 23 },
  { name: "Search", value: 18 },
  { name: "Referral", value: 11 },
  { name: "Email", value: 6 },
];

const timeOfDayData = [
  { time: "12am-4am", votes: 45 },
  { time: "4am-8am", votes: 78 },
  { time: "8am-12pm", votes: 185 },
  { time: "12pm-4pm", votes: 320 },
  { time: "4pm-8pm", votes: 416 },
  { time: "8pm-12am", votes: 278 },
];

const COLORS = ['#8884d8', '#83a6ed', '#8dd1e1', '#82ca9d', '#a4de6c', '#d0ed57'];

const AdvancedAnalytics: React.FC = () => {
  const [timeRange, setTimeRange] = useState("week");

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>User Engagement Analytics</CardTitle>
          <CardDescription>
            Detailed insights about user activity and engagement
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="engagement" className="w-full">
            <TabsList className="grid w-full grid-cols-3">
              <TabsTrigger value="engagement">Engagement Metrics</TabsTrigger>
              <TabsTrigger value="demographics">User Demographics</TabsTrigger>
              <TabsTrigger value="behavior">User Behavior</TabsTrigger>
            </TabsList>

            <TabsContent value="engagement" className="space-y-6">
              <div className="mt-6">
                <h3 className="text-lg font-medium mb-4">Weekly User Activity</h3>
                <div className="h-80">
                  <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={userEngagement} margin={{ top: 20, right: 30, left: 20, bottom: 5 }}>
                      <XAxis dataKey="day" />
                      <YAxis />
                      <Tooltip />
                      <Legend />
                      <Bar dataKey="new_users" name="New Users" fill="#8884d8" />
                      <Bar dataKey="returning" name="Returning Users" fill="#82ca9d" />
                      <Bar dataKey="votes" name="Total Votes" fill="#ffc658" />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <Card>
                  <CardHeader className="pb-2">
                    <CardTitle className="text-sm font-medium">Time of Day Activity</CardTitle>
                    <CardDescription>When users are most active</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="h-60">
                      <ResponsiveContainer width="100%" height="100%">
                        <LineChart data={timeOfDayData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                          <XAxis dataKey="time" />
                          <YAxis />
                          <Tooltip />
                          <Line type="monotone" dataKey="votes" stroke="#8884d8" activeDot={{ r: 8 }} />
                        </LineChart>
                      </ResponsiveContainer>
                    </div>
                  </CardContent>
                </Card>

                <Card>
                  <CardHeader className="pb-2">
                    <CardTitle className="text-sm font-medium">Key Metrics</CardTitle>
                    <CardDescription>User activity summary</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      <div className="flex items-center justify-between">
                        <div className="flex items-center">
                          <Users className="h-5 w-5 text-primary mr-2" />
                          <span>Active Users</span>
                        </div>
                        <span className="font-bold">1,245</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <div className="flex items-center">
                          <Activity className="h-5 w-5 text-primary mr-2" />
                          <span>Total Interactions</span>
                        </div>
                        <span className="font-bold">12,587</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <div className="flex items-center">
                          <Calendar className="h-5 w-5 text-primary mr-2" />
                          <span>Average User Age</span>
                        </div>
                        <span className="font-bold">32 days</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <div className="flex items-center">
                          <Clock className="h-5 w-5 text-primary mr-2" />
                          <span>Avg. Session Duration</span>
                        </div>
                        <span className="font-bold">3m 42s</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </TabsContent>

            <TabsContent value="demographics" className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <Card>
                  <CardHeader className="pb-2">
                    <CardTitle className="text-sm font-medium">Device Distribution</CardTitle>
                    <CardDescription>How users access the platform</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="h-60">
                      <ResponsiveContainer width="100%" height="100%">
                        <PieChart>
                          <Pie
                            data={deviceData}
                            cx="50%"
                            cy="50%"
                            labelLine={false}
                            label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                            outerRadius={80}
                            fill="#8884d8"
                            dataKey="value"
                          >
                            {deviceData.map((entry, index) => (
                              <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                            ))}
                          </Pie>
                          <Tooltip />
                        </PieChart>
                      </ResponsiveContainer>
                    </div>
                  </CardContent>
                </Card>

                <Card>
                  <CardHeader className="pb-2">
                    <CardTitle className="text-sm font-medium">Traffic Sources</CardTitle>
                    <CardDescription>Where users come from</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="h-60">
                      <ResponsiveContainer width="100%" height="100%">
                        <PieChart>
                          <Pie
                            data={trafficSourceData}
                            cx="50%"
                            cy="50%"
                            labelLine={false}
                            label={({ name, percent }) => `${name}: ${(percent * 100).toFixed(0)}%`}
                            outerRadius={80}
                            fill="#8884d8"
                            dataKey="value"
                          >
                            {trafficSourceData.map((entry, index) => (
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
            </TabsContent>

            <TabsContent value="behavior" className="space-y-6">
              <div className="mt-6">
                <h3 className="text-lg font-medium mb-4">User Flow Analysis</h3>
                <Card>
                  <CardContent className="p-6">
                    <div className="space-y-4">
                      <div className="p-4 border rounded-lg">
                        <h4 className="font-medium mb-2">Popular Poll Categories</h4>
                        <div className="grid grid-cols-2 gap-4">
                          <div className="flex items-center justify-between">
                            <span>Entertainment</span>
                            <span className="font-medium">32%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Politics</span>
                            <span className="font-medium">24%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Technology</span>
                            <span className="font-medium">18%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Sports</span>
                            <span className="font-medium">15%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Food</span>
                            <span className="font-medium">7%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Other</span>
                            <span className="font-medium">4%</span>
                          </div>
                        </div>
                      </div>

                      <div className="p-4 border rounded-lg">
                        <h4 className="font-medium mb-2">User Retention</h4>
                        <div className="space-y-2">
                          <div className="flex items-center justify-between">
                            <span>First Day Retention</span>
                            <span className="font-medium">78%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>7-Day Retention</span>
                            <span className="font-medium">52%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>30-Day Retention</span>
                            <span className="font-medium">34%</span>
                          </div>
                        </div>
                      </div>

                      <div className="p-4 border rounded-lg">
                        <h4 className="font-medium mb-2">Platform Feature Usage</h4>
                        <div className="space-y-2">
                          <div className="flex items-center justify-between">
                            <span>Voting</span>
                            <span className="font-medium">100%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Commenting</span>
                            <span className="font-medium">42%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Poll Creation</span>
                            <span className="font-medium">28%</span>
                          </div>
                          <div className="flex items-center justify-between">
                            <span>Social Sharing</span>
                            <span className="font-medium">18%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>
    </div>
  );
};

export default AdvancedAnalytics;
