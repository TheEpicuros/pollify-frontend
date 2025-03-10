
import React from "react";
import { Card, CardContent } from "@/components/ui/card";
import { ArrowUpRight } from "lucide-react";

interface MetricCardProps {
  title: string;
  value: string;
  change: string;
  trend: "up" | "down";
  icon: React.ReactNode;
}

const MetricCard: React.FC<MetricCardProps> = ({ title, value, change, trend, icon }) => (
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

export default MetricCard;
